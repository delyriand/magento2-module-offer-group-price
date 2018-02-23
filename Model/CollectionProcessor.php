<?php
/**
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade this module to newer
 * versions in the future.
 *
 * @category  Smile
 * @package   Smile\RetailerOfferGroupPrice
 * @author    Maxime LECLERCQ <maxime.leclercq@smile.fr>
 * @copyright 2018 Smile
 * @license   Open Software License ("OSL") v. 3.0
 */
namespace Smile\RetailerOfferGroupPrice\Model;

use Smile\ElasticsuiteCore\Search\Request\QueryInterface;
use Smile\RetailerOffer\Api\CollectionProcessorInterface;

/**
 * Collection processor.
 */
class CollectionProcessor extends \Smile\RetailerOffer\Model\CollectionProcessor implements CollectionProcessorInterface
{
    /**
     * @var \Smile\RetailerOffer\Helper\Offer
     */
    private $helper;

    /**
     * @var \Smile\StoreLocator\CustomerData\CurrentStore
     */
    private $currentStore;

    /**
     * ProductPlugin constructor.
     *
     * @param \Smile\RetailerOffer\Helper\Offer                         $offerHelper    The offer Helper
     * @param \Smile\StoreLocator\CustomerData\CurrentStore             $currentStore   The Retailer Data Object
     * @param \Smile\RetailerOffer\Helper\Settings                      $settingsHelper Settings Helper
     * @param \Smile\ElasticsuiteCore\Search\Request\Query\QueryFactory $queryFactory   Query Factory
     * @param \Magento\Framework\App\Config\ScopeConfigInterface        $scopeConfig    Scope Configuration
     */
    public function __construct(
        \Smile\RetailerOffer\Helper\Offer $offerHelper,
        \Smile\StoreLocator\CustomerData\CurrentStore $currentStore,
        \Smile\RetailerOffer\Helper\Settings $settingsHelper,
        \Smile\ElasticsuiteCore\Search\Request\Query\QueryFactory $queryFactory,
        \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig
    ) {
        parent::__construct($offerHelper, $currentStore, $settingsHelper, $queryFactory, $scopeConfig);
        $this->currentStore   = $currentStore;
        $this->helper         = $offerHelper;
    }

    /**
     * {@inheritdoc}
     */
    public function applyStoreLimitation(\Smile\ElasticsuiteCatalog\Model\ResourceModel\Product\Fulltext\Collection $collection)
    {
        if (!$this->settingsHelper->isDriveMode()) {
            return;
        }

        if ($this->getRetailerId()) {
            $mustClause     = ['must' => $this->getStoreLimitationMustClauses()];

            // If out of stock products must be shown, just keep filter on product having an offer for current
            // retailer, wether the offer is available or not.
            if (false === $this->settingsHelper->isEnabledShowOutOfStock()) {
                $mustClause['must'] = array_merge($mustClause['must'], $this->getStockMustClauses());
            }

            $boolFilter   = $this->queryFactory->create(QueryInterface::TYPE_BOOL, $mustClause);
            $nestedFilter = $this->queryFactory->create(QueryInterface::TYPE_NESTED, ['path' => 'offer', 'query' => $boolFilter]);

            $collection->addQueryFilter($nestedFilter);
        }
    }

    /**
     * @return array
     */
    public function getStoreLimitationMustClauses()
    {
        $retailerId = $this->getRetailerId();
        $sellerIdFilter = $this->queryFactory->create(
            QueryInterface::TYPE_TERM,
            ['field' => 'offer.seller_id', 'value' => $retailerId]
        );

        return [$sellerIdFilter];
    }

    /**
     * @return array
     */
    public function getStockMustClauses()
    {
        $isAvailableFilter = $this->queryFactory->create(
            QueryInterface::TYPE_TERM,
            ['field' => 'offer.is_available', 'value' => true]
        );

        return [$isAvailableFilter];
    }

    /**
     * Retrieve currently chosen retailer id
     *
     * @return int|null
     */
    private function getRetailerId()
    {
        $retailerId = null;
        if ($this->getRetailer()) {
            $retailerId = $this->getRetailer()->getId();
        }

        return $retailerId;
    }

    /**
     * Retrieve current retailer
     *
     * @return null|\Smile\Retailer\Api\Data\RetailerInterface
     */
    private function getRetailer()
    {
        $retailer = null;
        if ($this->currentStore->getRetailer() && $this->currentStore->getRetailer()->getId()) {
            $retailer = $this->currentStore->getRetailer();
        }

        return $retailer;
    }
}
