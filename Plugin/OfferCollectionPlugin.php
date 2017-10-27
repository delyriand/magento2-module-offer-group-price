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
 * @copyright 2017 Smile
 * @license   Open Software License ("OSL") v. 3.0
 */
namespace Smile\RetailerOfferGroupPrice\Plugin;

use Magento\Framework\Api\ExtensibleDataInterface;
use Magento\Framework\Api\SearchCriteriaBuilder;
use Smile\Offer\Api\Data\OfferInterface;
use Smile\Offer\Model\ResourceModel\Offer\Collection as OfferCollection;
use Smile\RetailerOfferGroupPrice\Api\Data\OfferGroupPriceInterface;
use Smile\RetailerOfferGroupPrice\Api\OfferGroupPriceRepositoryInterface;

/**
 * Customer group price offer collection plugin.
 */
class OfferCollectionPlugin
{
    /**
     * @var OfferGroupPriceRepositoryInterface
     */
    private $offerGroupPriceRepository;

    /**
     * @var SearchCriteriaBuilder
     */
    private $searchCriteriaBuilder;

    /**
     * OfferCollectionPlugin constructor.
     *
     * @param OfferGroupPriceRepositoryInterface $offerGroupPriceRepository Customer group price repository.
     * @param SearchCriteriaBuilder              $searchCriteriaBuilder     Search criteria builder.
     */
    public function __construct(
        OfferGroupPriceRepositoryInterface $offerGroupPriceRepository,
        SearchCriteriaBuilder $searchCriteriaBuilder
    ) {
        $this->offerGroupPriceRepository = $offerGroupPriceRepository;
        $this->searchCriteriaBuilder = $searchCriteriaBuilder;
    }

    /**
     * @param OfferCollection $collection Offer collection
     * @param \Closure        $proceed    Next proceed method
     * @param bool            $printQuery Print query
     * @param bool            $logQuery   Log query
     *
     * @return OfferCollection
     * @SuppressWarnings(PHPMD.BooleanArgumentFlag)
     * @SuppressWarnings(PHPMD.StaticAccess)
     */
    public function aroundLoad(OfferCollection $collection, \Closure $proceed, $printQuery = false, $logQuery = false)
    {
        if ($collection->isLoaded()) {
            return $collection;
        }

        \Magento\Framework\Profiler::start('SmileRetailerOfferGroupPrice:EXTENSIONS_ATTRIBUTES');
        $proceed($printQuery, $logQuery);
        $searchCriteria = $this->searchCriteriaBuilder
            ->addFilter(OfferGroupPriceInterface::FIELD_OFFER_ID, $collection->getAllIds(), 'in')
            ->create();
        $customerGroupPrices = $this->offerGroupPriceRepository->getList($searchCriteria)->getItems();
        /** @var OfferInterface $currentItem */
        foreach ($collection->getItems() as $currentItem) {
            $currentCustomerGroupPrices = array_filter(
                $customerGroupPrices,
                function ($currentCustomerGroupPrice) use ($currentItem) {
                    return $currentCustomerGroupPrice->getOfferId() == $currentItem->getId();
                }
            );
            $currentItem->getExtensionAttributes()->setCustomerGroupPrice($currentCustomerGroupPrices);
        }
        \Magento\Framework\Profiler::stop('SmileRetailerOfferGroupPrice:EXTENSIONS_ATTRIBUTES');

        return $collection;
    }
}
