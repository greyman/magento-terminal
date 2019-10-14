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
    private $_scopeConfig;
    private $_fileFactory;
    private $_csvProcessor;
    private $_directoryList;


    /**
     * Terminal constructor.
     * Instantiate a Totals object to store the terminals total data.
     */
    public function __construct(
        Totals $totals,
        ProductPricingRepository $pricingRepository,
        \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig,
        \Magento\Framework\App\Response\Http\FileFactory $fileFactory,
        \Magento\Framework\File\Csv $csvProcessor,
        \Magento\Framework\App\Filesystem\DirectoryList $directoryList
    )
    {
        $this->_totals = $totals;
        $this->_pricingRepository = $pricingRepository;
        $this->_scopeConfig = $scopeConfig;
        $this->_fileFactory = $fileFactory;
        $this->_csvProcessor = $csvProcessor;
        $this->_directoryList = $directoryList;
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

    public function getCartDownload()
    {
        if(! $this->allowCartDownload()){
            throw new \Magento\Framework\Exception\AuthorizationException(__('Not Authorised - please contact your admin'));
        }

        $fileName = 'basket_items.csv';
        $filePath = $this->_directoryList->getPath(\Magento\Framework\App\Filesystem\DirectoryList::VAR_DIR)
            . "/" . $fileName;

        $this->_csvProcessor
            ->setDelimiter(';')
            ->setEnclosure('"')
            ->saveData(
                $filePath,
                $this->_totals->getItems()
            );

        return $this->_fileFactory->create(
            $fileName,
            [
                'type' => "filename",
                'value' => $fileName,
                'rm' => true,
            ],
            \Magento\Framework\App\Filesystem\DirectoryList::VAR_DIR,
            'application/octet-stream'
        );

    }

    private function allowCartDownload()
    {
        return $this->_scopeConfig->getValue('cartitems/general/allow_download', \Magento\Store\Model\ScopeInterface::SCOPE_STORE);
    }

}