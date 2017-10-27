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
namespace Smile\RetailerOfferGroupPrice\Plugin\Ui\Component\Offer\Form;

use Magento\Framework\Registry;
use Smile\Offer\Api\Data\OfferInterface;
use Smile\RetailerOffer\Ui\Component\Offer\Form\DataProvider;

/**
 * Customer group price data provicer plugin.
 */
class DataProviderPlugin
{
    /**
     * @var Registry
     */
    private $registry;

    /**
     * DataProviderPlugin constructor.
     *
     * @param Registry $registry Registry.
     */
    public function __construct(Registry $registry)
    {
        $this->registry = $registry;
    }

    /**
     * Append artmag data to the Data provider data.
     *
     * @param DataProvider $dataProvider Data provider.
     * @param array|null   $data         Data.
     *
     * @return array
     */
    public function afterGetData(DataProvider $dataProvider, $data)
    {
        if ($data) {
            $offer = $this->getOffer($dataProvider);
            if ($offer !== null && $offer->getExtensionAttributes()->getCustomerGroupPrice()) {
                $itemKey = 0;
                foreach ($offer->getExtensionAttributes()->getCustomerGroupPrice() as $customerGroupPrice) {
                    $data[$offer->getId()]['group_price'][$itemKey++] = [
                        'id' => $customerGroupPrice->getId(),
                        'customer_group_id' => $customerGroupPrice->getCustomerGroupId(),
                        'price' => $customerGroupPrice->getPrice(),
                        'special_price' => $customerGroupPrice->getSpecialPrice(),
                    ];
                }
            }
        }

        return $data;
    }

    /**
     * @param DataProvider $dataProvider Data provider.
     *
     * @return null|OfferInterface
     */
    private function getOffer(DataProvider $dataProvider)
    {
        $offer = $this->registry->registry('current_offer');
        if (!$offer) {
            $offer = $dataProvider->getCollection()->getFirstItem();
        }

        return $offer instanceof OfferInterface ? $offer : null;
    }
}
