<?php
/**
 * Created by PhpStorm.
 * User: richard.grey
 * Date: 2019-10-14
 * Time: 19:55
 */

namespace RichardGrey\CheckoutTerminal\Model;

use Magento\Framework\DataObject;

class ProductPricing extends DataObject
{
    /**
     * @param $qty
     * @return float
     *
     * When passed a quantity this will return the total cost based on any volume discounts
     */
    public function totalPriceWithQty($qty) : float
    {
        if($this->getVolumeQty() == null || $qty < $this->getVolumeQty()){
            return $qty*$this->getUnitPrice();
        }

        $remainder_cost = ($qty % $this->getVolumeQty())*$this->getUnitPrice();
        $offer_cost = ($qty - ($qty % $this->getVolumeQty()))*$this->getVolumePrice();

        return $remainder_cost + $offer_cost;
    }
}