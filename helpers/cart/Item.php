<?php

/*
Shopping Cart Library by Danny Kay
All Rights Reserved
dannykay.developer@gmail.com
*/

namespace helpers\cart;

/**
 * Class Item
 * @package helpers\cart
 */
class Item implements \JsonSerializable
{
    protected $id    = null;
    protected $qty   = 0.0;
    protected $price = 0.0;
    protected $item  = null;

    /**
     * Item constructor.
     *
     * @param $item
     */
    function __construct($item = null)
    {
        if (isset($item)) {
            $this->price = $item->price;
            $this->item  = $item;
        }
    }

    /**
     * @return Item|null
     */
    public function getItem()
    {
        return $this->item;
    }

    /**
     * @return float
     */
    public function getQty(): float
    {
        return $this->qty;
    }

    /**
     * @return float
     */
    public function getPrice(): float
    {
        return $this->price;
    }

    /**
     * @return mixed|null
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @param $id
     *
     * @return $this
     */
    public function setId($id)
    {
        $this->id = $id;
        return $this;
    }

    /**
     * @param float $price
     *
     * @return $this
     */
    public function setPrice(float $price)
    {
        $this->price = $price;

        return $this;
    }

    /**
     * @param float $qty
     *
     * @return $this
     */
    public function setQty(float $qty)
    {
        $this->qty = $qty;

        return $this;
    }

    /**
     * @param $item
     *
     * @return $this
     */
    public function setItem($item)
    {
        $this->item = $item;

        return $this;
    }

    public function jsonSerialize()
    {
        return
            [
                'id'    => $this->getId(),
                'qty'   => $this->getQty(),
                'price' => $this->getPrice(),
                'item'  => $this->getItem(),
            ];

    }

    /**
     * @param $item
     *
     * @return Item
     */
    public static function newInstance($item)
    {
        return new Item($item);
    }

    /**
     * @param \stdClass $std
     *
     * @return Item
     */
    public static function importInstance(\stdClass $std)
    {
        $item = static::newInstance(null);

        $item->setQty($std->qty);
        $item->setPrice($std->price);

        $subItem = null;

        if (isset($std->item)) {
            $stdItem = $std->item;
            $subItem = static::newInstance(null);

            $subItem->setId($stdItem->id);
            $subItem->setQty($stdItem->qty);
            $subItem->setPrice($stdItem->price);
        }

        $item->setItem($subItem);

        return $item;
    }
}
