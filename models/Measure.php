<?php

namespace models;

/**
 * Class Measure
 * @package models
 */
class Measure extends BaseModel
{

    /**
     * @return string
     */
    public function name(): string
    {
        return 'Measure';
    }

    /**
     * @return array
     */
    public function getProductsAttribute()
    {
        $productModel = new Product($this->getConnection());

        return $this
            ->hasMany($productModel, 'Measure')
            ->fetchAll();
    }
}
