<?php
/**
 * Created by PhpStorm.
 * User: richard.grey
 * Date: 2019-10-14
 * Time: 18:56
 */

namespace RichardGrey\CheckoutTerminal\Test\Unit;


class checkoutTest extends \PHPUnit\Framework\TestCase
{
    protected $terminal;

    protected $case1;
    protected $case2;
    protected $case3;

    protected $expectedValueCase1;
    protected $expectedValueCase2;
    protected $expectedValueCase3;

    protected $pricing;


    public function setUp()
    {
        $objectManager = new \Magento\Framework\TestFramework\Unit\Helper\ObjectManager($this);

        $this->terminal = $objectManager->getObject('RichardGrey\CheckoutTerminal\Model\Terminal');
        $this->case1 = ['A','B','C','D','A','B','A','A'];
        $this->case2 = ['C','C','C','C','C','C','C'];
        $this->case3 = ['A','B','C','D'];
        $this->expectedValueCase1 = 32.40;
        $this->expectedValueCase2 = 7.25;
        $this->expectedValueCase3 = 15.40;
        $this->pricing = [
            ['Code'=>'A', 'Unit Price'=>'2.00', 'Volume Price'=>'1.75', 'Volume Qty' => 4],
            ['Code'=>'B', 'Unit Price'=>'12.00'],
            ['Code'=>'C', 'Unit Price'=>'1.25', 'Volume Price'=>'1', 'Volume Qty' => 6],
            ['Code'=>'D', 'Unit Price'=>'0.15']
        ];

        $this->terminal->setPricing($this->pricing);

    }

    public function testCase1()
    {
        foreach ($this->case1 as $item){
            $this->terminal->scan($item);
        }

        $this->assertEquals($this->expectedValueCase1, $this->terminal->getTotal());
    }

//    public function testCase2()
//    {
//        foreach ($this->case2 as $item){
//            $this->terminal->scan($item);
//        }
//
//        $this->assertEquals($this->expectedValueCase2, $this->terminal->getTotal());
//    }
//
//    public function testCase3()
//    {
//        foreach ($this->case3 as $item){
//            $this->terminal->scan($item);
//        }
//
//        $this->assertEquals($this->expectedValueCase3, $this->terminal->getTotal());
//    }


}