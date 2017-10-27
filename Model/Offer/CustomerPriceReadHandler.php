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
namespace Smile\RetailerOfferGroupPrice\Model\Offer;

use Magento\Framework\Api\SearchCriteriaBuilder;
use Magento\Framework\EntityManager\Operation\ExtensionInterface;
use Smile\Offer\Api\Data\OfferInterface;
use Smile\RetailerOfferGroupPrice\Api\Data\OfferGroupPriceInterface;
use Smile\RetailerOfferGroupPrice\Api\OfferGroupPriceRepositoryInterface;

/**
 * Customer group price read handler.
 */
class CustomerPriceReadHandler implements ExtensionInterface
{
    /**
     * @var OfferGroupPriceRepositoryInterface
     */
    protected $offerGroupPriceRepository;

    /**
     * @var SearchCriteriaBuilder
     */
    protected $searchCriteriaBuilder;

    /**
     * CustomerPriceReadHandler constructor.
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
     * Perform action on relation/extension attribute
     *
     * @param OfferInterface $entity    Offer.
     * @param array          $arguments Arguments.
     *
     * @return object|bool
     * @SuppressWarnings(PHPMD.UnusedFormalParameters)
     */
    public function execute($entity, $arguments = [])
    {
        $searchCriteria = $this->searchCriteriaBuilder
            ->addFilter(OfferGroupPriceInterface::FIELD_OFFER_ID, $entity->getId())
            ->create();
        $entity->getExtensionAttributes()->setCustomerGroupPrice(
            $this->offerGroupPriceRepository->getList($searchCriteria)->getItems()
        );

        return $entity;
    }
}
