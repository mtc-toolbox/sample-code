<?php

namespace models;

use helpers\cart\CartHelper;
use Greg\Orm\SqlException;

/**
 * Class Session
 * @package models
 */
class Session extends BaseModel
{
    const ERROR_INVALID_BALANCE_CODE    = 3;
    const ERROR_INVALID_BALANCE_MESSAGE = 'Invalid current balance.';

    const ERROR_MESSAGES = [
        self::ERROR_INVALID_BALANCE_CODE => self::ERROR_INVALID_BALANCE_MESSAGE,
    ];

    /**
     * @return string
     */
    public function name(): string
    {
        return 'Session';
    }

    /**
     * @return CartHelper
     */
    public function getSessionCart()
    {
        $cart = CartHelper::getInstance([$this, 'cartIdChecker']);

        if (!$this->cartIdChecker($cart->getCartId())) {
            $this->insert([
                'Id' => $cart->getCartId(),
            ]);
        }

        return $cart;
    }

    /**
     * @return false|string
     */
    public function getSessionCartJson()
    {
        return json_encode($this->getSessionCart());
    }

    /**
     * @param string $id
     *
     * @return int
     * @throws \Exception
     */
    public function cartIdChecker(string $id)
    {
        $this->clearError();

        try {
            $row = $this
                ->connection()
                ->select('Id')
                ->from($this->name())
                ->where('Id', $id)
                ->limit(1)
                ->fetchAll();

        } catch (SqlException $e) {

            $this->setError($e->getCode(), $e->getMessage());
            $row = [];

        } catch (Exception $e) {

            $this->setError($e->getCode(), $e->getMessage());
            $row = [];

        }

        return count($row);

    }

    /**
     * @return array|null
     */
    public function getCurrentSession()
    {
        $cart = $this->getSessionCart();

        $result = $this
            ->select('*')
            ->where('Id', $cart->getCartId())
            ->fetch();

        return $result;
    }

    /**
     * @return array
     */
    public function getSessionOrders()
    {
        $currentSession = $this->getCurrentSession();

        if (!isset($currentSession)) {
            return [];
        }
        $sessionOrderModel = new SessionOrder($this->getConnection());

        $result = $sessionOrderModel
            ->select('*')
            ->where('Session', $currentSession['Id'])
            ->fetchAll();

        return $result;
    }
}
