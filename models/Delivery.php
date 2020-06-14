<?php

namespace models;

use system\Request;

/**
 * Class Delivery
 * @package models
 */
class Delivery extends BaseModel
{
    const POST_ID   = 'taxId';
    const POST_COST = 'taxCost';

    /**
     * @return string
     */
    public function name(): string
    {
        return 'Delivery';
    }

    /**
     * @return array
     */
    public function getSessionOrdersAttribute()
    {
        $orderModel = new SessionOrder($this->getConnection());

        return $this
            ->hasMany($orderModel, 'Delivery')
            ->fetchAll();
    }

    /**
     * @param Request $request
     *
     * @return array|null
     */
    public function loadDeliveryData(Request $request)
    {
        $data = $request->post();

        $result = $this
            ->select('*')
            ->where('Id', $data[static::POST_ID])
            ->fetch();

        if ($result['Cost'] != $data[static::POST_COST]) {
            return null;
        }

        return $result;
    }

}
