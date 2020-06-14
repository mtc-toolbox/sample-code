<?php
/*
Shopping Cart Library by Danny Kay
All Rights Reserved
dannykay.developer@gmail.com
*/

namespace helpers\cart;

use system\Request;

/**
 * Class CartHelper
 * @package helpers\cart
 */
class CartHelper implements \JsonSerializable
{
    const SESSION_VAR_NAME = 'cart';
    /**
     * @var Item[]
     */
    private $items = [];
    /**
     * @var string
     */
    private $cartId;

    /**
     * @var float
     */
    private $totalQty = 0.0;

    /**
     * @var float
     */
    private $totalPrice = 0.0;


    /**
     * CartHelper constructor.
     *
     * @param               $oldCart
     * @param callable|null $uniqueIdChecker
     */
    function __construct($oldCart, callable $uniqueIdChecker = null)
    {
        // fill the cart with old cart items
        if ($oldCart) {
            $this->items      = $oldCart->items;
            $this->totalQty   = $oldCart->totalQty;
            $this->totalPrice = $oldCart->totalPrice;
            $this->cartId     = $oldCart->cartId;
        } else {
            $this->cartId = static::buildCartId($uniqueIdChecker);
            $this->createSession();
        }
    }

    /**
     * @param array $items
     *
     * @return $this
     */
    public function setItems(array $items)
    {
        $this->items = $items;

        return $this;
    }

    /**
     * @param string $cartId
     *
     * @return $this
     */
    public function setCartId(string $cartId)
    {
        $this->cartId = $cartId;

        return $this;
    }

    /**
     * @param float $totalPrice
     *
     * @return $this
     */
    public function setTotalPrice(float $totalPrice)
    {
        $this->totalPrice = $totalPrice;

        return $this;
    }

    /**
     * @param float $totalQty
     *
     * @return $this
     */
    public function setTotalQty(float $totalQty)
    {
        $this->totalQty = $totalQty;

        return $this;
    }

    /**
     * @return array
     */
    public function jsonSerialize()
    {
        return
            [
                'cartId'     => $this->getCartId(),
                'totalQty'   => $this->getTotalQty(),
                'totalPrice' => $this->getTotalPrice(),
                'items'      => $this->getItems(),

            ];
    }

    /**
     * @param string $data
     */
    public static function setData(string $data)
    {
        $parsedData = json_decode($data);

        $parsedData->items = (array)$parsedData->items;

        $parsedItems = [];

        foreach ($parsedData->items as $key => $item) {
            if (isset($item)) {

                $itemInstance = Item::importInstance($item);

                $subItem = $itemInstance->getItem();

                if ($subItem->getId()) {
                    $parsedItems[$subItem->getId()] = $item;
                }

                $parsedData->items[$key] = Item::importInstance($item);
            } else {
                unset($parsedData->items[$key]);
            }
        }

        $_SESSION[static::SESSION_VAR_NAME] = $parsedData;

    }

    /**
     * @return bool
     */
    public static function hasCorrectSession()
    {
        if (!isset($_SESSION[static::SESSION_VAR_NAME])) {
            return false;
        };

        $cart = $_SESSION[static::SESSION_VAR_NAME];

        if (!is_object($cart)) {
            return false;
        };

        if (!isset($cart->cartId)) {
            return false;
        }

        if (!strlen($cart->cartId)) {
            return false;
        }

        if (!isset($cart->items)) {
            return false;
        }
        if (!isset($cart->totalQty)) {
            return false;
        }
        if (!isset($cart->totalPrice)) {
            return false;
        }

        if (!is_array($cart->items)) {
            return false;
        }

        return true;
    }

    /**
     * @return CartHelper
     */
    public static function getInstance(callable $uniqueIdChecker = null)
    {
        $oldCart = static::hasCorrectSession() ? $_SESSION[static::SESSION_VAR_NAME] : null;

        return new CartHelper($oldCart, $uniqueIdChecker);
    }

    /**
     * @param Item $item
     * @param      $qty
     *
     * @return $this
     */
    public function add(Item $item, $qty)
    {

        if ($qty > 0 && $item != null) {

            $storedItem = Item::newInstance($item);
            if (!empty($this->items)) {
                if (array_key_exists($item->getId(), $this->items)) {
                    // putting the incoming item into a variable
                    $storedItem = $this->items[$item->getId()];
                }
            }
            // increase cart items, price and quantity
            $storedItem->setQty($storedItem->getQty() + $qty);
            $storedItem->setPrice($item->getPrice() * $storedItem->getQty());
            $this->items[$item->getId()] = $storedItem;
            $this->setTotalPrice($this->getTotalPrice() + $item->getPrice() * $qty);
            $this->setTotalQty($this->getTotalQty() + qty);
            $_SESSION[static::SESSION_VAR_NAME] = $this;
        }

        return $this;
    }

    /**
     * @return $this
     */
    public function clear()
    {
        unset($_SESSION[static::SESSION_VAR_NAME]);

        return $this;
    }

    /**
     * @return $this
     */
    public function clearItems()
    {
        $this
            ->setItems([])
            ->setTotalPrice(0.0)
            ->setTotalQty(0);

        $_SESSION[static::SESSION_VAR_NAME] = $this;

        return $this;
    }

    /**
     * @return $this
     */
    public function createSession()
    {
        $_SESSION[static::SESSION_VAR_NAME] = $this;

        return $this;
    }

    /**
     * @param Item $item
     * @param      $qty
     *
     * @return $this
     */
    public function reduce(Item $item, $qty)
    {
        // pass the item to be reduced and the quantity of items to be removed
        if (
            !empty($this->items)
            && $this->totalQty > 0
            && $qty > 0
            && $item != null
            && array_key_exists($item->getId(), $this->items)
            && $this->getSubQty($item) > 1
        ) {
            $storedItem = Item::newInstance($item);
            if ($this->items) {
                if (array_key_exists($item->getId(), $this->items)) {
                    // putting the incoming item into a variable
                    $storedItem = $this->items[$item->getId()];
                }
            }

            // remove the specified quantity from the cart
            $storedItem->setQty($storedItem->getQty() - $qty);
            $storedItem->setPrice($item->getPrice() * $storedItem->getQty());
            $this->items[$item->getId()] = $storedItem;

            if ($this . getSubQty($item) > 0) {
                $_SESSION[static::SESSION_VAR_NAME] = $this;
            }

        }

        return $this;
    }

    /**
     * @param $item
     * @param $qty
     */
    public function update($item, $qty)
    {
        // update cart items

        if ($qty > 0 && $item != null && array_key_exists($item->getId(), $this->getItems())) {
            $this
                ->remove($item)
                ->add($item, $qty);
        }

        return $this;
    }

    /**
     * @param Item $item
     */
    public function remove(Item $item)
    {

        if ($item != null && array_key_exists($item->getId(), $this->getItems())) {
            $this->totalQty   -= $this->getSubQty($item);
            $this->totalPrice -= $this->getSubPrice($item);
            unset($this->getItems()[$item->getId]);
            $_SESSION[static::SESSION_VAR_NAME] = $this;
        }

        return $this;
    }

    /**
     * @return $this|null
     */
    public function all()
    {
        if (!empty($this->items)) {
            return $this;
        }

        return null;
    }

    /**
     * @param Item $item
     *
     * @return mixed|null
     */
    public function get(Item $item)
    {
        if (!empty($this->items) && $item != null) {
            return $this->items[$item->getId()];
        }

        return null;
    }

    /**
     * @param int $id
     *
     * @return Item|null
     */
    public function getById(int $id)
    {
        foreach ($this->items as $item) {
            $subItem = $item->getItem() ?? null;
            if (isset($subItem)) {
                if ($subItem->getId() == $id) {
                    return $item;
                }
            }
        }
        return null;
    }

    /**
     * @return mixed
     */
    public function getLastItem()
    {
        if (!empty($this->items)) {
            return end($this->items);
        }

        return null;
    }

    /**
     * @return mixed
     */
    public function getFirstItem()
    {
        if (!empty($this->items)) {
            return reset($this->items);
        }

        return null;
    }

    /**
     * @return float
     */
    public function getTotalQty()
    {
        return $this->totalQty;
    }

    /**
     * @return float
     */
    public function getTotalPrice()
    {
        return $this->totalPrice;
    }

    /**
     * @param Item $item
     *
     * @return float
     */
    public function getSubQty(Item $item)
    {
        if (!empty($this->items) && $item != null && array_key_exists($item->getId(), $this->items)) {
            return $this->items[$item->getId()]->getQty();
        }

        return 0.0;
    }

    /**
     * @param Item $item
     *
     * @return float
     */
    public function getSubPrice(Item $item)
    {
        if (!empty($this->items && $item != null && array_key_exists($item->getId(), $this->items))) {
            return $this->items[$item->getId()]->getPrice();
        }

        return 0.0;
    }

    /**
     * @return string
     */
    public function getCartId()
    {
        return $this->cartId;
    }

    /**
     * @return int
     */
    public function getItemQty()
    {
        return count($this->items);
    }

    /**
     * @return array
     */
    public function getItems(): array
    {
        return $this->items;
    }

    /**
     * @return array
     */
    public function getItemIds(): array
    {
        $ids = [];
        foreach ($this->items as $item) {
            $subItem = $item->getItem();

            if (isset($subItem)) {
                $ids[] = $subItem->getId();
            }
        }

        return $ids;
    }

    /**
     * @param callable $uniqueIdChecker
     *
     * @return string
     */
    public static function buildCartId(callable $uniqueIdChecker)
    {
        do {
            $stringToHash = (string)microtime(true);
            $id           = sha1($stringToHash);

        } while ($uniqueIdChecker($id));

        return $id;
    }

    /**
     * @param Request $request
     */
    public static function setPostData(Request $request)
    {
        $data = $request->post();
        static::setData($data[static::SESSION_VAR_NAME]);

    }
}

session_start();
