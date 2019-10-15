# magento-terminal
## Part 1
```<?php

$a = '1'; //Assign '1' to the variable $a
$b = &$a; //Assign by reference the value $a to the variable $b
$b = "2$b"; //Concatinate 2 to the variable $b and re-assign to $b
echo $a.", ".$b; //Will echo 21, 21 as the variable $b is a copy of $a rather than a new variable. When we update $b, $a will have the same value.
```

## Part 2

```<?php
/*
 * Below is my prototype application in vanilla php
 */

/**
 * Class Terminal
 * Top level service class for the terminal
 */
class Terminal {
    /**
     * @var Totals
     */
    private $totals;
    /**
     * @var The total cart value to be returned with $terminal->total
     */
    public $total;
    /**
     * @var ProductPricingRepository
     * Used to store the Product pricing data
     */
    private $_pricing_repo;

    /**
     * Terminal constructor.
     * Instantiate a Totals object to store the terminals total data.
     */
    public function __construct()
    {
        $this->totals = new Totals;
    }

    /**
     * @param array $pricing
     * Sets the pricing by accepting an array
     */
    public function setPricing(array $pricing): void
    {
        $pricing_repo = new ProductPricingRepository($pricing);
        $this->_pricing_repo = $pricing_repo;
    }

    /**
     * @param string $item
     * The method to be called when adding a products to the cart
     */
    public function scan(string $item): void
    {
        $product = $this->_pricing_repo->getProduct($item);
        $this->totals->addItem($product);
        $this->total = $this->totals->getTotal();
    }
}

/**
 * Class Product
 * Simple DTO class for storing product data and it's associated pricing object.
 */
class Product {

    private $_pricing;

    private $_code;

    public function setCode(string $code)
    {
        $this->_code = $code;
    }

    public function setPricing(ProductPricing $pricing)
    {
        $this->_pricing = $pricing;
    }

    public function getCode(): string
    {
        return $this->_code;
    }

    public function getPricing() : ProductPricing
    {
        return $this->_pricing;
    }

}

/**
 * Class ProductPricingRepository
 * A simple repository which allows the instantiation of a collection of products and their respective prices.
 */
class ProductPricingRepository {

    private $_productCollection = [];

    /**
     * ProductPricingRepository constructor.
     * @param array $product_pricing
     */
    public function __construct(array $product_pricing)
    {
        foreach ($product_pricing as $product_price){
            $product = new Product;
            $product->setCode($product_price['Code']);
            $pricing = new ProductPricing;
            $pricing->setUnitPrice($product_price['Unit Price']);
            if(isset($product_price['Volume Price'])){
                $pricing->setVolumePrice($product_price['Volume Price']);
                $pricing->setVolumeQty($product_price['Volume Qty']);
            }
            $product->setPricing($pricing);
            $this->_productCollection[] = $product;
        }
    }

    /**
     * @param string $code
     * @return Product
     *
     * Returns a Product object based on the product code.
     */
    public function getProduct(string $code) : Product
    {
        $products = array_filter(
            $this->_productCollection,
            function(Product $product) use ($code){
                return $product->getCode() == $code;
        });

        return reset($products);
    }

}

/**
 * Class ProductPricing
 * A class to store the product pricing data including the volume qty at which the volume prices are applicable.
 * It also makes a total calculation based on a given quantitiy value.
 */
class ProductPricing {

    private $_unit_price;
    private $_volume_price;
    private $_volume_qty;

    public function setUnitPrice($price)
    {
        $this->_unit_price = $price;
    }

    public function setVolumePrice($price)
    {
        $this->_volume_price = $price;
    }

    public function setVolumeQty(int $qty)
    {
        $this->_volume_qty = $qty;
    }

    public function getUnitPrice() : float
    {
        return $this->_unit_price;
    }

    public function getVolumePrice() : float
    {
        return $this->_volume_price;
    }

    public function getVolumeQty() : int
    {
        return $this->_volume_qty;
    }

    /**
     * @param $qty
     * @return float
     *
     * When passed a quantity this will return the total cost based on any volume discounts
     */
    public function totalPriceWithQty($qty) : float
    {
        if(! isset($this->_volume_qty) || $qty < $this->_volume_qty){
            return $qty*$this->getUnitPrice();
        }

        $remainder_cost = ($qty % $this->_volume_qty)*$this->getUnitPrice();

        $offer_cost = ($qty - ($qty % $this->_volume_qty))*$this->getVolumePrice();

        return $remainder_cost + $offer_cost;
    }

}

/**
 * Class Totals
 * Used to store the totals data in any given cart.
 */
class Totals {

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

$pricing = [
    ['Code'=>'A', 'Unit Price'=>'2.00', 'Volume Price'=>'1.75', 'Volume Qty' => 4],
    ['Code'=>'B', 'Unit Price'=>'12.00'],
    ['Code'=>'C', 'Unit Price'=>'1.25', 'Volume Price'=>'1', 'Volume Qty' => 6],
    ['Code'=>'D', 'Unit Price'=>'0.15']
];

$terminal = new Terminal;
$terminal->setPricing($pricing);
$terminal->scan('A');
$terminal->scan('B');
$terminal->scan('C');
$terminal->scan('D');
$terminal->scan('A');
$terminal->scan('B');
$terminal->scan('A');
$terminal->scan('A');


echo "\n".money_format('%i',$terminal->total);


```
## Part 3
[See wiki https://github.com/greyman/magento-terminal/wiki/Part-3](https://github.com/greyman/magento-terminal/wiki/Part-3)
