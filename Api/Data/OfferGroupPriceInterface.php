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
namespace Smile\RetailerOfferGroupPrice\Api\Data;

/**
 * Customer group price interface.
 */
interface OfferGroupPriceInterface
{
    const TABLE_NAME = 'smile_offer_group_price';

    const FIELD_ID = 'id';
    const FIELD_OFFER_ID = 'offer_id';
    const FIELD_CUSTOMER_GROUP_ID = 'customer_group_id';
    const FIELD_PRICE = 'price';
    const FIELD_SPECIAL_PRICE = 'special_price';

    /**
     * @see \Magento\Framework\Model\AbstractModel::getId()
     * @return int
     */
    public function getId();

    /**
     * @return int
     */
    public function getOfferId();

    /**
     * @return int
     */
    public function getCustomerGroupId();

    /**
     * @return float
     */
    public function getPrice();

    /**
     * @return float
     */
    public function getSpecialPrice();

    /**
     * @see \Magento\Framework\Model\AbstractModel::setId()
     * @param int $objectId Object ID.
     *
     * @return $this
     */
    public function setId($objectId);

    /**
     * @param int $offerId Offer ID.
     *
     * @return $this
     */
    public function setOfferId($offerId);

    /**
     * @param int $customerGroupId Customer group ID.
     *
     * @return $this
     */
    public function setCustomerGroupId($customerGroupId);

    /**
     * @param float $price Price
     *
     * @return $this
     */
    public function setPrice($price);

    /**
     * @param float $specialPrice Special price.
     *
     * @return $this
     */
    public function setSpecialPrice($specialPrice);

    /**
     * @param array $data Data.
     *
     * @return $this
     */
    public function populateFromArray(array $data);
}
