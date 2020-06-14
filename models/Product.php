<?php

namespace models;

use helpers\cart\CartHelper;
use helpers\cart\Item;

/**
 * Class Product
 * @package models
 */
class Product extends BaseModel
{
    const INVALID_CART_CONTENT_CODE    = 1;
    const INVALID_CART_CONTENT_MESSAGE = 'Cart has incostistent values';

    const ERROR_MESSAGES = [
        self::INVALID_CART_CONTENT_CODE => self::INVALID_CART_CONTENT_MESSAGE,
    ];

    /**
     * @return string
     */
    public function name(): string
    {
        return 'Product';
    }

    /**
     * @return array
     */
    public function getSessionOrderProductsAttribute()
    {
        $orderProductModel = new SessionOrderProduct($this->getConnection());

        return $this
            ->hasMany($orderProductModel, 'Product')
            ->fetchAll();
    }

    /**
     * @return array|null
     */
    public function getPreviewImageDataAttribute()
    {
        $image = new Image($this->getConnection());

        $result = $image
            ->select('*')
            ->where('Id', $this['PreviewImage'])
            ->fetch();

        return $result;
    }

    /**
     * @return array
     */
    public function getProductList(CartHelper $cart)
    {
        $cartId = $cart->getCartId();

        $result = $this
            ->select(
                'Product.Code',
                'Product.Id',
                'Product.Name',
                'Product.Cost',
                'Product.AverageRate',
                'Product.RateCount',
                'Product.MeasureSize',
                'Measure.Short',
                'Measure.Decimals',
                'Measure.InputStep',
                'Image.Filename',
                'Image.Title',
                'Rate.Id as RateId'
            )
            ->leftJoin('Image', 'Product.PreviewImage = Image.Id')
            ->leftJoin('Rate', 'Rate.Product = Product.Id AND Rate.Session = \'' . $cartId . '\'')
            ->leftJoin('Measure', 'Product.Measure = Measure.Id')
            ->orderBy('Product.Name')
            ->fetchAll();

        return $result;
    }

    /**
     * @return array
     */
    public function getCartProductList(CartHelper $cart)
    {
        $cartId = $cart->getCartId();

        $cartProducts = $cart->getItemIds();

        $data = [];

        if (count($cartProducts)) {
            $data = $this
                ->select(
                    'Product.Code',
                    'Product.Id',
                    'Product.Name',
                    'Product.Cost',
                    'Product.AverageRate',
                    'Product.RateCount',
                    'Product.MeasureSize',
                    'Measure.Short',
                    'Measure.Decimals',
                    'Measure.InputStep',
                    'Image.Filename',
                    'Image.Title',
                    'Rate.Id as RateId'
                )
                ->leftJoin('Image', 'Product.PreviewImage = Image.Id')
                ->leftJoin('Rate', 'Rate.Product = Product.Id AND Rate.Session = \'' . $cartId . '\'')
                ->leftJoin('Measure', 'Product.Measure = Measure.Id')
                ->where('Product.Id', 'IN', $cartProducts)
                ->orderBy('Product.Name')
                ->fetchAll();
        }

        $res = $this
            ->select(
                'Product.Code',
                'Product.Id',
                'Product.Name',
                'Product.Cost',
                'Product.AverageRate',
                'Product.RateCount',
                'Product.MeasureSize',
                'Measure.Short',
                'Measure.Decimals',
                'Measure.InputStep',
                'Image.Filename',
                'Image.Title',
                'Rate.Id as RateId'
            )
            ->leftJoin('Image', 'Product.PreviewImage = Image.Id')
            ->leftJoin('Rate', 'Rate.Product = Product.Id AND Rate.Session = \'' . $cartId . '\'')
            ->leftJoin('Measure', 'Product.Measure = Measure.Id')
            ->where('Product.Id', 'IN', $cartProducts)->toSql();

        $result = [];

        foreach ($data as $datum) {

            $item = $cart->getById($datum['Id']);

            $subItem = $item->getItem();

            if (isset($subItem)) {
                $datum['RealCost'] = $datum['Cost'];
                $datum['Cost']     = $subItem->getPrice();
            }
            $datum['Quantity'] = $item->getQty();
            $result[]          = $datum;
        }

        return $result;
    }

    /**
     * @param CartHelper $cart
     *
     * @return bool
     */
    public function isActualCart(CartHelper $cart): bool
    {
        $products = $this->getCartProductList($cart);

        foreach ($products as $product) {
            if ($product['Cost'] != $product['RealCost']) {
                return false;
            }
        }

        return true;
    }

    /**
     * @param CartHelper $cart
     *
     * @return bool
     */
    public function fixCartData(CartHelper $cart)
    {
        $result   = true;
        $products = $this->getCartProductList($cart);

        foreach ($products as $product) {

            if ($product['Cost'] != $product['RealCost']) {

                if ($result) {
                    $this->setErrorByCode(static::INVALID_CART_CONTENT_CODE);
                    $result = false;
                }

                $item = new Item();

                $item
                    ->setId($product['Id'])
                    ->setPrice($product['RealCost']);

                $cart->update($item, $product['Quantity']);
            }
        }

        return $result;
    }
}
