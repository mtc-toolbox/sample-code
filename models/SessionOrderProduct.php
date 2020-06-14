<?php

namespace models;

/**
 * Class SessionOrderProduct
 * @package models
 */
class SessionOrderProduct extends BaseModel
{

    /**
     * @return string
     */
    public function name(): string
    {
        return 'SessionOrderProduct';
    }

    /**
     * @return array|null
     */
    public function getSessionOrderDataAttribute()
    {
        $sessionOrder = new SessionOrder($this->getConnection());

        $result = $sessionOrder
            ->select('*')
            ->where('Id', $this['SessionOrder'])
            ->fetch();

        return $result;
    }

    /**
     * @return array|null
     */
    public function getProductDataAttribute()
    {
        $product = new Product($this->getConnection());

        $result = $product
            ->select('*')
            ->where('Id', $this['Product'])
            ->fetch();

        return $result;
    }

}
