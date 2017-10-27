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
namespace Smile\RetailerOfferGroupPrice\Model\Data;

use Magento\Framework\DataObject\IdentityInterface;
use Magento\Framework\Model\AbstractModel;
use Smile\RetailerOfferGroupPrice\Api\Data\OfferGroupPriceInterface;

/**
 * Customer group price model.
 */
class OfferGroupPrice extends AbstractModel implements OfferGroupPriceInterface, IdentityInterface
{
    const CACHE_TAG = 'offer_group_price';

    /**
     * @return int
     */
    public function getOfferId()
    {
        return $this->getData(self::FIELD_OFFER_ID);
    }

    /**
     * @return int
     */
    public function getCustomerGroupId()
    {
        return $this->getData(self::FIELD_CUSTOMER_GROUP_ID);
    }

    /**
     * @return float
     */
    public function getPrice()
    {
        return $this->getData(self::FIELD_PRICE);
    }

    /**
     * @return float
     */
    public function getSpecialPrice()
    {
        return $this->getData(self::FIELD_SPECIAL_PRICE);
    }

    /**
     * @param int $offerId Offer ID.
     *
     * @return $this
     */
    public function setOfferId($offerId)
    {
        return $this->setData(self::FIELD_OFFER_ID, $offerId);
    }

    /**
     * @param int $customerGroupId Customer group ID.
     *
     * @return $this
     */
    public function setCustomerGroupId($customerGroupId)
    {
        return $this->setData(self::FIELD_CUSTOMER_GROUP_ID, $customerGroupId);
    }

    /**
     * @param float $price Price
     *
     * @return $this
     */
    public function setPrice($price)
    {
        return $this->setData(self::FIELD_PRICE, $price);
    }

    /**
     * @param float $specialPrice Special price.
     *
     * @return $this
     */
    public function setSpecialPrice($specialPrice)
    {
        return $this->setData(self::FIELD_SPECIAL_PRICE, $specialPrice);
    }

    /**
     * Return unique ID(s) for each object in system
     *
     * @return string[]
     */
    public function getIdentities()
    {
        return [self::CACHE_TAG.'_'.$this->getId()];
    }

    /**
     * @param array $data Data.
     *
     * @return $this
     */
    public function populateFromArray(array $data)
    {
        $this
            ->setCustomerGroupId($data['customer_group_id'])
            ->setPrice($data['price'])
            ->setSpecialPrice($data['special_price'])
            ->setOfferId($data['offer_id']);

        return $this;
    }

    /**
     * @SuppressWarnings(PHPMD.CamelCaseMethodName)
     */
    protected function _construct()
    {
        $this->_init(\Smile\RetailerOfferGroupPrice\Model\ResourceModel\OfferGroupPrice::class);
    }
}
