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
namespace Smile\RetailerOfferGroupPrice\Api\Data;

use Magento\Framework\Api\SearchResultsInterface;

/**
 * Customer group price results interface.
 */
interface GroupPriceItemResultsInterface extends SearchResultsInterface
{
    /**
     * Get stock list.
     *
     * @return \Smile\RetailerOfferGroupPrice\Api\Data\OfferGroupPriceInterface[]
     */
    public function getItems();

    /**
     * Set stock list.
     *
     * @param \Smile\RetailerOfferGroupPrice\Api\Data\OfferGroupPriceInterface[] $items Items.
     *
     * @return $this
     */
    public function setItems(array $items);

    /**
     * Get search criteria.
     *
     * @return \Magento\Framework\Api\SearchCriteriaInterface
     */
    public function getSearchCriteria();
}
