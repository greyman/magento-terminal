<?php
/**
 * Created by PhpStorm.
 * User: richard.grey
 * Date: 2019-10-15
 * Time: 11:54
 */

namespace RichardGrey\CheckoutTerminal\Test\Unit\Model;


class ProductPricingTest extends \PHPUnit\Framework\TestCase
{
    protected $product_pricing;
    protected $total_price_with_discount;
    protected $total_price_without_discount;
    protected $qty;

    public function setUp()
    {
        $objectManager = new \Magento\Framework\TestFramework\Unit\Helper\ObjectManager($this);
        $this->product_pricing = $objectManager->getObject('RichardGrey\CheckoutTerminal\Model\ProductPricing');
        $this->total_price_with_discount = '9';
        $this->total_price_without_discount = '10';
        $this->qty = 5;
    }

    public function testTotalPriceWithQtyWithoutDiscount()
    {
        $this->product_pricing->setUnitPrice('2.00');
        $this->assertEquals($this->total_price_without_discount, $this->product_pricing->totalPriceWithQty($this->qty));
    }

    public function testTotalPriceWithQtyWithDiscount()
    {
        $this->product_pricing->setUnitPrice('2.00');
        $this->product_pricing->setVolumePrice('1.75');
        $this->product_pricing->setVolumeQty(4);
        $this->assertEquals($this->total_price_with_discount, $this->product_pricing->totalPriceWithQty($this->qty));
    }


}