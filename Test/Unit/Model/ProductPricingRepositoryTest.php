<?php
/**
 * Created by PhpStorm.
 * User: richard.grey
 * Date: 2019-10-15
 * Time: 12:28
 */

namespace RichardGrey\CheckoutTerminal\Test\Unit\Model;


use RichardGrey\CheckoutTerminal\Model\Product;
use RichardGrey\CheckoutTerminal\Model\ProductPricing;

/**
 * Class ProductPricingRepositoryTest
 * @package RichardGrey\CheckoutTerminal\Test\Unit\Model
 */
class ProductPricingRepositoryTest extends \PHPUnit\Framework\TestCase
{

    protected $pricing;
    protected $product_pricing_repository;
    protected $productFactoryMock;
    protected $productPricingFactoryMock;
    protected $productMock;
    protected $productPricingMock;

    public function setUp()
    {
        $this->productFactoryMock = $this->getMockBuilder(
            \RichardGrey\CheckoutTerminal\Model\ProductFactory::class
        )->disableOriginalConstructor()->setMethods(['create'])->getMock();

        $this->productPricingFactoryMock = $this->getMockBuilder(
            \RichardGrey\CheckoutTerminal\Model\ProductPricingFactory::class
        )->disableOriginalConstructor()->setMethods(['create'])->getMock();

        $this->productMock = $this->getMockBuilder(
            \RichardGrey\CheckoutTerminal\Model\Product::class
        )->disableOriginalConstructor()->setMethods(['setCode', 'setPricing', 'getCode', 'getPricing'])->getMock();

        $this->productPricingMock = $this->getMockBuilder(
            \RichardGrey\CheckoutTerminal\Model\ProductPricing::class
        )->disableOriginalConstructor()->setMethods(['setUnitPrice', 'setVolumePrice', 'setVolumeQty'])->getMock();

        $objectManager = new \Magento\Framework\TestFramework\Unit\Helper\ObjectManager($this);

        $this->product_pricing_repository = $objectManager->getObject('RichardGrey\CheckoutTerminal\Model\ProductPricingRepository',
            [
                'productFactory'=> $this->productFactoryMock,
                'productPricingFactory' => $this->productPricingFactoryMock
            ]
        );

        $this->pricing = [
            ['Code'=>'A', 'Unit Price'=>'2.00', 'Volume Price'=>'1.75', 'Volume Qty' => 4],
        ];

        $this->productFactoryMock->expects($this->any())
            ->method('create')
            ->willReturn($this->productMock);

        $this->productPricingFactoryMock->expects($this->any())
            ->method('create')
            ->willReturn($this->productPricingMock);

        $this->productPricingMock->expects($this->any())
            ->method('setUnitPrice')
            ->with('2.00')
            ->willReturnSelf();

        $this->productPricingMock->expects($this->any())
            ->method('setVolumePrice')
            ->with('1.75')
            ->willReturnSelf();

        $this->productPricingMock->expects($this->any())
            ->method('setVolumeQty')
            ->with('4')
            ->willReturnSelf();

        $this->productMock->expects($this->any())
            ->method('setCode')
            ->with('A')
            ->willReturnSelf();

        $this->productMock->expects($this->any())
            ->method('getCode')
            ->willReturn('A');

        $this->productMock->expects($this->any())
            ->method('getPricing')
            ->willReturn($this->productPricingMock);

        $this->productMock->expects($this->any())
            ->method('setPricing')
            ->with($this->productPricingMock)
            ->willReturnSelf();
    }

    public function testSetPricing()
    {
        $this->assertNull($this->product_pricing_repository->setPricing($this->pricing));
        return $this->product_pricing_repository;
    }

    /**
     * @depends testSetPricing
     */
    public function testGetProduct($repository)
    {
        $product = $repository->getProduct('A');
        $this->assertInstanceOf(Product::class, $product);
        $this->assertEquals('A', $product->getCode());
        $this->assertInstanceOf(ProductPricing::class, $product->getPricing());
    }

}
