<?php

namespace models;

/**
 * Class Image
 * @package models
 */
class Image extends BaseModel
{
    /**
     * @return string
     */
    public function name(): string
    {
        return 'Image';
    }

    /**
     * @return array
     */
    public function getPreviewProductsAttribute()
    {
        $productModel = new Product($this->getConnection());

        return $this
            ->hasMany($productModel, 'PreviewImage')
            ->fetchAll();
    }
}
