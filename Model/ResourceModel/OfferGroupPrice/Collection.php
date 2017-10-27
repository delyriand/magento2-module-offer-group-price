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
namespace Smile\RetailerOfferGroupPrice\Model\ResourceModel\OfferGroupPrice;

use Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection;

/**
 * Customer group price collection.
 */
class Collection extends AbstractCollection
{
    /**
     * Define resource model
     *
     * @SuppressWarnings(PHPMD.CamelCaseMethodName)
     */
    protected function _construct()
    {
        $this->_init(
            \Smile\RetailerOfferGroupPrice\Model\Data\OfferGroupPrice::class,
            \Smile\RetailerOfferGroupPrice\Model\ResourceModel\OfferGroupPrice::class
        );
    }
}
