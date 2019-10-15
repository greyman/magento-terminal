<?php
/**
 * Created by PhpStorm.
 * User: richard.grey
 * Date: 2019-10-15
 * Time: 15:13
 */

namespace RichardGrey\CheckoutTerminal\Test\Unit\Model;

class TerminalTest extends \PHPUnit\Framework\TestCase
{
    protected $pricing;
    protected $terminal;
    protected $totals;
    protected $pricingRepositry;
    protected $scopeconfig;
    protected $filefactory;
    protected $csvProcessor;
    protected $directoryList;
    protected $product;
    protected $productPricing;

    public function setUp()
    {
        $this->totals = $this->getMockBuilder(
            \RichardGrey\CheckoutTerminal\Model\Totals::class
        )->disableOriginalConstructor()->setMethods(['addItem', 'getItems', 'getTotal'])->getMock();

        $this->pricingRepositry = $this->getMockBuilder(
            \RichardGrey\CheckoutTerminal\Model\ProductPricingRepository::class
        )->disableOriginalConstructor()->setMethods(['getProduct', 'setPricing'])->getMock();

        $this->scopeconfig = $this->getMockBuilder(
            \Magento\Framework\App\Config\ScopeConfigInterface::class
        )->disableOriginalConstructor()->setMethods(['getValue', 'isSetFlag'])->getMock();

        $this->filefactory = $this->getMockBuilder(
            \Magento\Framework\App\Response\Http\FileFactory::class
        )->disableOriginalConstructor()->setMethods(['create'])->getMock();

        $this->csvProcessor = $this->getMockBuilder(
            \Magento\Framework\File\Csv::class
        )->disableOriginalConstructor()->setMethods(['setDelimiter', 'setEnclosure', 'saveData'])->getMock();

        $this->directoryList = $this->getMockBuilder(
            \Magento\Framework\App\Filesystem\DirectoryList::class
        )->disableOriginalConstructor()->setMethods(['getPath'])->getMock();

        $this->product = $this->getMockBuilder(
            \RichardGrey\CheckoutTerminal\Model\Product::class
        )->disableOriginalConstructor()->setMethods(['setCode', 'setPricing', 'getCode', 'getPricing'])->getMock();

        $this->productPricing = $this->getMockBuilder(
            \RichardGrey\CheckoutTerminal\Model\ProductPricing::class
        )->disableOriginalConstructor()->setMethods(['setUnitPrice', 'setVolumePrice', 'setVolumeQty'])->getMock();


        $objectManager = new \Magento\Framework\TestFramework\Unit\Helper\ObjectManager($this);

        $this->terminal = $objectManager->getObject('RichardGrey\CheckoutTerminal\Model\Terminal', [
            'totals' => $this->totals,
            'pricingRepository' => $this->pricingRepositry,
            'scopeConfig' => $this->scopeconfig ,
            'fileFactory' => $this->filefactory,
            'csvProcessor' => $this->csvProcessor,
            'directoryList' => $this->directoryList
        ]);

        $this->pricing = [
            ['Code'=>'A', 'Unit Price'=>'2.00', 'Volume Price'=>'1.75', 'Volume Qty' => 4],
        ];

        $this->product->expects($this->any())
            ->method('setCode')
            ->with('A')
            ->willReturnSelf();

        $this->product->expects($this->any())
            ->method('getCode')
            ->willReturn('A');

        $this->product->expects($this->any())
            ->method('getPricing')
            ->willReturn($this->productPricing);

        $this->product->expects($this->any())
            ->method('setPricing')
            ->with($this->productPricing)
            ->willReturnSelf();

        $this->pricingRepositry->expects($this->any())
            ->method('setPricing')
            ->with($this->pricing)
            ->willReturnSelf();

        $this->pricingRepositry->expects($this->any())
            ->method('getProduct')
            ->willReturn($this->product);

        $this->totals->expects($this->any())
            ->method('addItem')
            ->with($this->product)
            ->willReturnSelf();

        $this->totals->expects($this->any())
            ->method('getTotal')
            ->willReturn('2.00');

        $this->productPricing->expects($this->any())
            ->method('setUnitPrice')
            ->with('2.00')
            ->willReturnSelf();

        $this->productPricing->expects($this->any())
            ->method('setVolumePrice')
            ->with('1.75')
            ->willReturnSelf();

        $this->productPricing->expects($this->any())
            ->method('setVolumeQty')
            ->with('4')
            ->willReturnSelf();
    }

    public function testSetPricing()
    {
        $this->assertNull($this->terminal->setPricing($this->pricing));
        return $this->terminal;
    }

    /**
     * @depends testSetPricing
     */
    public function testScan($terminal)
    {
        $this->assertNull($terminal->scan('A'));
        return $this->terminal;
    }

}