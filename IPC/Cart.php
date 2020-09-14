<?php

namespace Mypos\IPC;

/**
 * Purchase cart object
 */
class Cart
{
    const ITEM_TYPE_ARTICLE = 'article';
    const ITEM_TYPE_DELIVERY = 'delivery';
    const ITEM_TYPE_DISCOUNT = 'discount';

    /**
     * Array containing cart items
     *
     * @var array
     */
    private $cart;

    /**
     *
     * @param string $itemName Item name
     * @param int $quantity Items quantity
     * @param float $price Single item price
     *
     * @param string $type
     * @return Cart
     * @throws IPC_Exception
     */
    public function add($itemName, $quantity, $price, $type = self::ITEM_TYPE_ARTICLE)
    {
        if (empty($itemName)) {
            throw new IPC_Exception('Invalid cart item name');
        }

        if (empty($quantity) || !Helper::isValidCartQuantity($quantity)) {
            throw new IPC_Exception('Invalid cart item quantity');
        }

        if (empty($price) || !Helper::isValidAmount($price)) {
            throw new IPC_Exception('Invalid cart item price');
        }

        $item = [
            'name' => $itemName,
            'quantity' => $quantity,
            'price' => $price,
        ];

        switch ($type) {
            case self::ITEM_TYPE_DELIVERY:
                $item['delivery'] = 1;
                break;
            case self::ITEM_TYPE_DISCOUNT:
                $item['price'] = $num = -1 * abs($item['price']);
                break;
        }

        $this->cart[] = $item;

        return $this;
    }

    /**
     * Returns cart total amount
     *
     * @return float
     */
    public function getTotal()
    {
        $sum = 0;
        if (!empty($this->cart)) {
            foreach ($this->cart as $v) {
                $sum += $v['quantity'] * $v['price'];
            }
        }

        return $sum;
    }

    /**
     * Returns count of items in cart
     *
     * @return int
     */
    public function getItemsCount()
    {
        return (is_array($this->cart) && !empty($this->cart)) ? count($this->cart) : 0;
    }

    /**
     * Validate cart items
     *
     * @return boolean
     * @throws IPC_Exception
     */
    public function validate()
    {
        if (!$this->getCart() || count($this->getCart()) == 0) {
            throw new IPC_Exception('Missing cart items');
        }

        return true;
    }

    /**
     * Return cart array
     *
     * @return array
     */
    public function getCart()
    {
        return $this->cart;
    }
}
