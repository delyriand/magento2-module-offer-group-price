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
namespace Smile\RetailerOfferGroupPrice\Plugin\Model\Product\Indexer\Fulltext\Datasource;

use Smile\Offer\Model\Product\Indexer\Fulltext\Datasource\OfferData;
use Smile\RetailerOfferGroupPrice\Model\ResourceModel\Product\Indexer\Fulltext\Datasource\OfferPriceData as ResourceModel;

/**
 * Offer data source plugin.
 */
class OfferDataPlugin
{

    /**
     * @var ResourceModel
     */
    private $resourceModel;

    /**
     * OfferDataPlugin constructor.
     *
     * @param ResourceModel $resourceModel Resource model.
     */
    public function __construct(ResourceModel $resourceModel)
    {
        $this->resourceModel = $resourceModel;
    }

    /**
     * Add customer group price data in offer.
     *
     * @param OfferData $source    Offer data.
     * @param array     $indexData Index data.
     *
     * @return array
     * @SuppressWarnings(PHPMD.UnusedFormalParameters)
     */
    public function afterAddData(OfferData $source, array $indexData)
    {
        $offersPricesData = $this->resourceModel->loadOfferPriceData(array_keys($indexData));
        $customerGroupIds = $this->resourceModel->getCustomerGroupIds();
        foreach ($indexData as $productId => $productData) {
            $oldOfferData = $productData['offer'] ?? [];
            $offersData = [];
            foreach ($customerGroupIds as $customerGroupId) {
                foreach ($oldOfferData as $offer) {
                    $offerPricesData = $offersPricesData[$offer['offer_id'].'-'.$customerGroupId] ?? [];
                    $offer['customer_group_id'] = $customerGroupId;
                    if ($offerPricesData) {
                        $offer['original_price'] = $offerPricesData['price'];
                        $offer['price'] = $offer['original_price'];
                        if ($offerPricesData['special_price'] > 0) {
                            $offer['price'] = min($offer['price'], $offerPricesData['special_price']);
                        }
                        $offer['is_discount'] = $offer['price'] < $offer['original_price'];
                    }
                    $offersData[] = $offer;
                }
            }
            $indexData[$productId]['offer'] = $offersData;
        }

        return $indexData;
    }
}
