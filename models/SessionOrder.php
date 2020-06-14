<?php

namespace models;

use helpers\cart\CartHelper;
use helpers\cart\Item;

/**
 * Class SessionOrder
 * @package models
 */
class SessionOrder extends BaseModel
{

    /**
     * @return string
     */
    public function name(): string
    {
        return 'SessionOrder';
    }

    /**
     * @return array|null
     */
    public function getSessionDataAttribute()
    {
        $session = new Session($this->getConnection());

        $result = $session
            ->select('*')
            ->where('Id', $this['Session'])
            ->fetch();

        return $result;
    }

    /**
     * @return array|null
     */
    public function getDeliveryDataAttribute()
    {
        $delivery = new Delivery($this->getConnection());

        $result = $delivery
            ->select('*')
            ->where('Id', $this['Delivery'])
            ->fetch();

        return $result;
    }

    /**
     * @return array
     */
    public function getSessionOrderProductsAttribute()
    {
        $sessionOrderProductModel = new SessionOrderProduct($this->getConnection());

        return $this
            ->hasMany($sessionOrderProductModel, 'SessionOrder')
            ->fetchAll();
    }

    /**
     * @param CartHelper $cart
     * @param array      $tax
     *
     * @return bool
     */
    public function insertFromCart(CartHelper $cart, array $tax)
    {
        $insertedCount = $this->insert([
                'Session'     => $cart->getCartId(),
                'BTime'       => time(),
                'Total'       => $cart->getTotalPrice(),
                'Delivery'    => $tax['Id'],
                'DeliveryTax' => $tax['Cost'],
            ]
        );


        if (!$insertedCount) {
            $this->setErrorByCode(static::SERVER_ERROR_CODE);

            return false;
        }

        $result = $this->addProductsFromCart($cart);

        return $result;
    }

    /**
     * @param CartHelper $cart
     *
     * @return bool
     */
    public function addProductsFromCart(CartHelper $cart)
    {
        $items     = $cart->getItems();
        $currentId = $this->getConnection()->lastInsertId();

        /* @var Item[] $items */
        foreach ($items as $item) {

            $product  = new SessionOrderProduct($this->getConnection());
            $baseItem = $item->getItem();

            $baseItem = $baseItem ?? $item;

            $inserted = $product->insert([
                'SessionOrder' => $currentId,
                'Product'      => $baseItem->getId(),
                'Quantity'     => $item->getQty(),
                'Cost'         => round($item->getPrice(), 2),

            ]);

            if (!$inserted) {
                $this->setErrorByCode(static::SERVER_ERROR_CODE);

                return false;
            }
        }

        return true;
    }

    /**
     * @param CartHelper $cart
     * @param array      $tax
     *
     * @return bool
     */
    public function saveCart(CartHelper $cart, array $tax)
    {
        $this->clearError();
        $connection = $this->getConnection();
        if (!$connection->beginTransaction()) {
            $this->setErrorByCode(static::SERVER_ERROR_CODE);

            return false;
        }
        $productModel = new Product($connection);
        if (!$productModel->fixCartData($cart)) {
            $this->setError($productModel->getErrorCode(), $productModel->getErrorMessage());
            $connection->rollBack();

            return false;
        }
        $sessionModel      = new Session($this->getConnection());
        $sessionData       = $sessionModel
            ->select('*')
            ->where('Id', $cart->getCartId())
            ->fetch();
        $roundedTotalPrice = round($cart->getTotalPrice(), 2);
        if ($sessionData['Balance'] < $roundedTotalPrice + $tax['Cost']) {
            $this->setError(Session::ERROR_INVALID_BALANCE_CODE, Session::ERROR_INVALID_BALANCE_MESSAGE);

            return false;
        }
        $orders = $this->insertFromCart($cart, $tax);
        if (!$orders) {
            $connection->rollBack();

            return false;
        }
        $sessionModel
            ->where('Id', $sessionData['Id'])
            ->update([
                'Expenses' => $sessionData['Expenses'] + $roundedTotalPrice + $tax['Cost'],
                'Balance'  => $sessionData['Balance'] - $roundedTotalPrice - $tax['Cost'],

            ]);
        if (!$connection->commit()) {
            $this->setErrorByCode(static::SERVER_ERROR_CODE);

            return false;
        }
        $cart->clearItems();

        return true;
    }
}
