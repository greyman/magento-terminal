<?php
/**
 * Created by PhpStorm.
 * User: richard.grey
 * Date: 2019-10-14
 * Time: 19:56
 */

namespace RichardGrey\CheckoutTerminal\Model;

class Totals
{
    /**
     * @var array
     * Used to store the product prices in the
     */
    private $items = [];

    /**
     * @param Product $product
     * Add the pricing data to the $items array.
     */
    public function addItem(Product $product): void
    {
        $code = $product->getCode();
        $this->items[$code]['items'][] = ['Price'=>$product->getPricing()->getUnitPrice()];
        $this->items[$code]['total'] = $product->getPricing()->totalPriceWithQty(count($this->items[$code]['items']));
    }

    /**
     * @return float
     * Returns the cart total.
     */
    public function getTotal(): float
    {
        $total = 0;
        foreach ($this->items as $item){
            $total += $item['total'];
        }

        return $total;
    }
}