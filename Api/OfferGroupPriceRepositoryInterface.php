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
namespace Smile\RetailerOfferGroupPrice\Api;

use Smile\RetailerOfferGroupPrice\Api\Data\OfferGroupPriceInterface;

/**
 * Customer group price repository interface.
 */
interface OfferGroupPriceRepositoryInterface
{
    /**
     * @param OfferGroupPriceInterface $offerGroupPrice Customer group price.
     *
     * @return OfferGroupPriceInterface
     * @throws \Magento\Framework\Exception\CouldNotSaveException
     */
    public function save(OfferGroupPriceInterface $offerGroupPrice) : OfferGroupPriceInterface;

    /**
     * Retrieve customer group price matching the specified criteria.
     *
     * @param \Magento\Framework\Api\SearchCriteriaInterface $searchCriteria Search criteria.
     *
     * @return \Smile\RetailerOfferGroupPrice\Api\Data\GroupPriceItemResultsInterface
     */
    public function getList(\Magento\Framework\Api\SearchCriteriaInterface $searchCriteria);

    /**
     * @param OfferGroupPriceInterface $offerGroupPrice Customer group price.
     *
     * @return boolean
     * @throws \Magento\Framework\Exception\CouldNotDeleteException
     */
    public function delete(OfferGroupPriceInterface $offerGroupPrice);
}
