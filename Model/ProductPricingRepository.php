<?php
/**
 * Created by PhpStorm.
 * User: richard.grey
 * Date: 2019-10-14
 * Time: 19:55
 */

namespace RichardGrey\CheckoutTerminal\Model;

use RichardGrey\CheckoutTerminal\Model\ProductFactory;
use RichardGrey\CheckoutTerminal\Model\ProductPricingFactory;

class ProductPricingRepository
{
    private $_productCollection = [];
    protected $_productFactory;
    protected $_productPricingFactory;

    public function __construct(
        ProductFactory $productFactory,
        ProductPricingFactory $productPricingFactory
    )
    {
        $this->_productFactory = $productFactory;
        $this->_productPricingFactory = $productPricingFactory;
    }

    /**
     * ProductPricingRepository constructor.
     * @param array $product_pricing
     */
    public function setPricing(array $product_pricing)
    {
        foreach ($product_pricing as $product_price){
            $product = $this->createProduct();
            $product->setCode($product_price['Code']);
            $pricing = $this->createProductPricing();
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
     * @return ProductPricing
     */
    private function createProductPricing()
    {
        return $this->_productPricingFactory->create();
    }

    /**
     * @return Product
     */
    private function createProduct()
    {
        return $this->_productFactory->create();
    }

    /**
     * @param string $code
     * @return Product
     *
     * Returns a Product object based on the product code.
     */
    public function getProduct(string $code) //: Product
    {
        $products = array_filter(
            $this->_productCollection,
            function($product) use ($code){
                return $product->getCode() == $code;
            });

        return reset($products);
    }
}
