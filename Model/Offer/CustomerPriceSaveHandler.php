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

use Magento\Framework\EntityManager\Operation\ExtensionInterface;
use Smile\Offer\Api\Data\OfferInterface;
use Smile\RetailerOfferGroupPrice\Api\Data\OfferGroupPriceInterface;
use Smile\RetailerOfferGroupPrice\Api\Data\OfferGroupPriceInterfaceFactory;
use Smile\RetailerOfferGroupPrice\Api\OfferGroupPriceRepositoryInterface;

/**
 * Customer group price save handler.
 */
class CustomerPriceSaveHandler implements ExtensionInterface
{
    /**
     * @var OfferGroupPriceInterfaceFactory
     */
    protected $offerGroupPriceFactory;

    /**
     * @var OfferGroupPriceRepositoryInterface
     */
    protected $offerGroupPriceRepository;

    /**
     * CustomerPriceSaveHandler constructor.
     *
     * @param OfferGroupPriceInterfaceFactory    $offerGroupPriceFactory    Customer group price factory.
     * @param OfferGroupPriceRepositoryInterface $offerGroupPriceRepository Customer group price repository.
     */
    public function __construct(
        OfferGroupPriceInterfaceFactory $offerGroupPriceFactory,
        OfferGroupPriceRepositoryInterface $offerGroupPriceRepository
    ) {
        $this->offerGroupPriceFactory = $offerGroupPriceFactory;
        $this->offerGroupPriceRepository = $offerGroupPriceRepository;
    }

    /**
     * Perform action on relation/extension attribute
     *
     * @param OfferInterface $entity    Offer
     * @param array          $arguments Arguments
     *
     * @return object|bool
     * @throws \Magento\Framework\Exception\CouldNotSaveException
     * @SuppressWarnings(PHPMD.UnusedFormalParameters)
     * @throws \Magento\Framework\Exception\CouldNotDeleteException
     */
    public function execute($entity, $arguments = [])
    {
        $groupPrice = $entity->getGroupPrice();
        if (!empty($groupPrice)) {
            $savedIds = [];
            foreach ($groupPrice as $price) {
                $price[OfferGroupPriceInterface::FIELD_OFFER_ID] = $entity->getId();
                $offerGroupPrice = $this->getOfferGroupPrice($entity, $price['id']);
                $offerGroupPrice->populateFromArray($price);
                $this->offerGroupPriceRepository->save($offerGroupPrice);
                $savedIds[] = $price['id'];
            }
            $customerGroupPricesToDelete = array_filter(
                $entity->getExtensionAttributes()->getCustomerGroupPrice(),
                function ($currentOfferGroupPrices) use ($savedIds) {
                    return !in_array($currentOfferGroupPrices->getId(), $savedIds);
                }
            );
            foreach ($customerGroupPricesToDelete as $groupPrice) {
                $this->offerGroupPriceRepository->delete($groupPrice);
            }
        }

        return $entity;
    }

    /**
     * @param OfferInterface $entity            Offer
     * @param int|string     $offerGroupPriceId Customer group price ID
     *
     * @return mixed|OfferGroupPriceInterface
     */
    protected function getOfferGroupPrice(OfferInterface $entity, $offerGroupPriceId)
    {
        /** @var OfferGroupPriceInterface $offerGroupPrice */
        $offerGroupPrice = $this->offerGroupPriceFactory->create();
        if ($entity->getExtensionAttributes()->getCustomerGroupPrice() && $offerGroupPriceId !== '') {
            $matchingOfferGroupPrices = array_filter(
                $entity->getExtensionAttributes()->getCustomerGroupPrice(),
                function ($currentOfferGroupPrices) use ($offerGroupPriceId) {
                    return $currentOfferGroupPrices->getId() == $offerGroupPriceId;
                }
            );
            $offerGroupPrice = $matchingOfferGroupPrices ? reset($matchingOfferGroupPrices) : $offerGroupPrice;
        }

        return $offerGroupPrice;
    }
}
