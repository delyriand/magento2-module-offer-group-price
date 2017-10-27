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

use Magento\Framework\Exception\CouldNotDeleteException;
use Magento\Framework\Exception\CouldNotSaveException;
use Smile\RetailerOfferGroupPrice\Api\Data\OfferGroupPriceInterface;
use Smile\RetailerOfferGroupPrice\Api\Data\OfferGroupPriceInterfaceFactory;
use Smile\RetailerOfferGroupPrice\Api\OfferGroupPriceRepositoryInterface;
use Smile\RetailerOfferGroupPrice\Api\Data\GroupPriceItemResultsInterface;
use Smile\RetailerOfferGroupPrice\Api\Data\GroupPriceItemResultsInterfaceFactory;
use Magento\Framework\Api\SearchCriteria\CollectionProcessorInterface as CollectionProcessor;
use Magento\Framework\Data\Collection\AbstractDb as AbstractCollection;
use Smile\RetailerOfferGroupPrice\Model\ResourceModel\OfferGroupPrice as OfferGroupPriceResourceModel;

/**
 * Customer group price repository.
 */
class OfferGroupPriceRepository implements OfferGroupPriceRepositoryInterface
{
    /**
     * @var OfferGroupPriceInterfaceFactory
     */
    protected $objectFactory;

    /**
     * @var GroupPriceItemResultsInterfaceFactory
     */
    protected $searchResultsFactory;

    /**
     * @var CollectionProcessor
     */
    protected $collectionProcessor;

    /**
     * @var OfferGroupPriceResourceModel
     */
    protected $objectResource;

    /**
     * OfferGroupPriceRepository constructor.
     *
     * @param OfferGroupPriceInterfaceFactory       $objectFactory        Object Factory.
     * @param GroupPriceItemResultsInterfaceFactory $searchResultsFactory Search results factory.
     * @param CollectionProcessor                   $collectionProcessor  Collection processor.
     * @param OfferGroupPriceResourceModel          $objectResource       Object resource.
     */
    public function __construct(
        OfferGroupPriceInterfaceFactory $objectFactory,
        GroupPriceItemResultsInterfaceFactory $searchResultsFactory,
        CollectionProcessor $collectionProcessor,
        OfferGroupPriceResourceModel $objectResource
    ) {
        $this->objectFactory = $objectFactory;
        $this->searchResultsFactory = $searchResultsFactory;
        $this->collectionProcessor = $collectionProcessor;
        $this->objectResource = $objectResource;
    }

    /**
     * @param OfferGroupPriceInterface $offerGroupPrice Customer group price.
     *
     * @return OfferGroupPriceInterface
     * @throws \Magento\Framework\Exception\CouldNotSaveException
     */
    public function save(OfferGroupPriceInterface $offerGroupPrice): OfferGroupPriceInterface
    {
        try {
            /** @var \Magento\Framework\Model\AbstractModel $offerGroupPrice */
            $this->objectResource->save($offerGroupPrice);
        } catch (\Exception $e) {
            throw new CouldNotSaveException(__($e->getMessage()));
        }

        return $offerGroupPrice;
    }

    /**
     * Retrieve customer group price matching the specified criteria.
     *
     * @param \Magento\Framework\Api\SearchCriteriaInterface $searchCriteria Search criteria.
     *
     * @return \Smile\RetailerOfferGroupPrice\Api\Data\GroupPriceItemResultsInterface
     */
    public function getList(\Magento\Framework\Api\SearchCriteriaInterface $searchCriteria)
    {
        /** @var AbstractCollection $collection */
        $collection = $this->objectFactory->create()->getCollection();

        /** @var GroupPriceItemResultsInterface $searchResults */
        $searchResults = $this->searchResultsFactory->create();
        if ($searchCriteria) {
            $searchResults->setSearchCriteria($searchCriteria);
            $this->collectionProcessor->process($searchCriteria, $collection);
        }

        $collection->load();
        $searchResults->setTotalCount($collection->getSize());
        $searchResults->setItems($collection->getItems());

        return $searchResults;
    }

    /**
     * @param OfferGroupPriceInterface $offerGroupPrice Customer group price.
     *
     * @return boolean
     * @throws CouldNotDeleteException
     */
    public function delete(OfferGroupPriceInterface $offerGroupPrice)
    {
        try {
            /** @var \Magento\Framework\Model\AbstractModel $stockItem */
            $this->objectResource->delete($offerGroupPrice);
        } catch (\Exception $e) {
            throw new CouldNotDeleteException(__($e->getMessage()));
        }

        return true;
    }
}
