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
namespace Smile\RetailerOfferGroupPrice\Model\ResourceModel\Product\Indexer\Fulltext\Datasource;

use Smile\ElasticsuiteCatalog\Model\ResourceModel\Eav\Indexer\Indexer;
use Smile\Offer\Api\Data\OfferInterface;
use Smile\RetailerOfferGroupPrice\Api\Data\OfferGroupPriceInterface;

/**
 * Offer price data resource model
 */
class OfferPriceData extends Indexer
{
    /**
     * Load offer prices data for a list of product ids.
     *
     * @param array $productIds Product ids list.
     *
     * @return array
     */
    public function loadOfferPriceData($productIds)
    {
        $select = $this->getConnection()->select()
            ->from(
                ['op' => $this->getTable(OfferGroupPriceInterface::TABLE_NAME)],
                [
                    new \Zend_Db_Expr(
                        'concat(
                        op.'.OfferGroupPriceInterface::FIELD_OFFER_ID.', '
                        .'"-", '
                        .'op.'.OfferGroupPriceInterface::FIELD_CUSTOMER_GROUP_ID
                        .')'
                    ),
                    OfferGroupPriceInterface::FIELD_OFFER_ID,
                    OfferGroupPriceInterface::FIELD_CUSTOMER_GROUP_ID,
                    OfferGroupPriceInterface::FIELD_PRICE,
                    OfferGroupPriceInterface::FIELD_SPECIAL_PRICE,
                ]
            )
            ->joinLeft(
                ['o' => $this->getTable('smile_offer')],
                'o.'.OfferInterface::OFFER_ID.' = op.'.OfferGroupPriceInterface::FIELD_OFFER_ID,
                [OfferInterface::SELLER_ID, OfferInterface::PRODUCT_ID]
            )
            ->where('o.'.OfferInterface::PRODUCT_ID.' IN(?)', $productIds);

        return $this->getConnection()->fetchAssoc($select);
    }

    /**
     * Return customer group ids.
     *
     * @return array
     */
    public function getCustomerGroupIds()
    {
        $select = $this->getConnection()->select()
            ->from($this->getTable('customer_group'), ['customer_group_id']);

        return $this->getConnection()->fetchCol($select);
    }
}
