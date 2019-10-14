<?php
/**
 * Created by PhpStorm.
 * User: richard.grey
 * Date: 2019-10-14
 * Time: 19:54
 */

namespace RichardGrey\CheckoutTerminal\Model;


class Terminal
{
    /**
     * @var Totals
     */
    private $_totals;
    /**
     * @var The total cart value to be returned with $terminal->total
     */
    public $total;
    /**
     * @var ProductPricingRepository
     * Used to store the Product pricing data
     */
    private $_pricingRepository;

    /**
     * Terminal constructor.
     * Instantiate a Totals object to store the terminals total data.
     */
    public function __construct(
        Totals $totals,
        ProductPricingRepository $pricingRepository
    )
    {
        $this->_totals = $totals;
        $this->_pricingRepository = $pricingRepository;
    }

    /**
     * @param array $pricing
     * Sets the pricing by accepting an array
     */
    public function setPricing(array $pricing): void
    {
        $this->_pricingRepository->setPricing($pricing);
    }

    /**
     * @param string $item
     * The method to be called when adding a products to the cart
     */
    public function scan(string $item): void
    {
        $product = $this->_pricingRepository->getProduct($item);
        $this->_totals->addItem($product);
        $this->total = $this->_totals->getTotal();
    }

    public function getTotal()
    {
        return $this->total;
    }

}