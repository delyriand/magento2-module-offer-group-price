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
namespace Smile\RetailerOfferGroupPrice\Plugin;

use Magento\Catalog\Model\Product;
use Magento\Framework\App\ObjectManager;
use Magento\Framework\App\State;
use Magento\Framework\DataObjectFactory;
use Smile\Offer\Api\Data\OfferInterface;
use Smile\StoreLocator\CustomerData\CurrentStore;
use Smile\RetailerOffer\Helper\Offer as OfferHelper;
use Smile\RetailerOffer\Helper\Settings;
use Magento\Customer\Model\Session as CustomerSession;

/**
 * Product plugin for customer group price.
 */
class ProductPlugin
{
    /**
     * @var OfferHelper
     */
    private $helper;

    /**
     * @var CurrentStore
     */
    private $currentStore;

    /**
     * @var \Smile\RetailerOffer\Helper\Settings
     */
    private $settingsHelper;

    /**
     * @var CustomerSession
     */
    private $customerSession;

    /**
     * ProductPlugin constructor.
     *
     * @param OfferHelper     $offerHelper     The offer Helper
     * @param CurrentStore    $currentStore    The Retailer Data Object
     * @param State           $state           The Application State
     * @param Settings        $settingsHelper  Settings Helper
     * @param CustomerSession $customerSession Customer session.
     */
    public function __construct(
        OfferHelper $offerHelper,
        CurrentStore $currentStore,
        State $state,
        Settings $settingsHelper,
        CustomerSession $customerSession
    ) {
        $this->currentStore   = $currentStore;
        $this->helper         = $offerHelper;
        $this->state          = $state;
        $this->settingsHelper = $settingsHelper;
        $this->customerSession = $customerSession;
    }

    /**
     * @param Product  $product Product
     * @param \Closure $proceed Next proceed method
     *
     * @return mixed
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function aroundGetPrice(Product $product, \Closure $proceed)
    {
        $price = $proceed();
        $price = $this->applyCustomerGroupPrice($product, $price);

        return $price;
    }

    /**
     * @param Product  $product Product
     * @param \Closure $proceed Next proceed method
     *
     * @return mixed
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function aroundGetFinalPrice(Product $product, \Closure $proceed)
    {
        $price = $proceed();
        $price = $this->applyCustomerGroupPrice($product, $price);

        return $price;
    }

    /**
     * @param Product $product Product
     * @param float   $price   Price.
     *
     * @return mixed
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    private function applyCustomerGroupPrice(Product $product, $price)
    {
        $customerGroupPrice = $this->getCustomerGroupPrice($product);
        if ($customerGroupPrice) {
            $price = min($customerGroupPrice, $price);
        }

        return $price;
    }

    /**
     * @param Product $product Product
     *
     * @return null|float
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    private function getCustomerGroupPrice(Product $product)
    {
        $offer = $this->getCurrentOffer($product);
        if (!$offer || !$this->useStoreOffers()) {
            return null;
        }
        $customerGroupPrice = null;
        $customerGroupPrices = $offer->getExtensionAttributes()->getCustomerGroupPrice();
        if ($customerGroupPrices) {
            $customerGroupPrices = array_filter($customerGroupPrices, function ($customerGroupPrice) use ($product) {
                return $this->getCustomerGroupId($product) == $customerGroupPrice->getCustomerGroupId();
            });
            $customerGroupPrice = reset($customerGroupPrices);
        }

        return $customerGroupPrice ? $customerGroupPrice->getPrice() : null;
    }

    /**
     * @param Product $product Product
     *
     * @return int
     */
    private function getCustomerGroupId(Product $product)
    {
        if ($product->getCustomerGroupId() !== null) {
            return $product->getCustomerGroupId();
        }

        return \Magento\Framework\App\ObjectManager::getInstance()
            ->get('Magento\Customer\Model\Session')->getCustomerGroupId();
    }

    /**
     * Retrieve Current Offer for the product.
     *
     * @param Product $product The product
     *
     * @return OfferInterface
     */
    private function getCurrentOffer($product)
    {
        $offer      = null;
        $retailerId = null;
        if ($this->currentStore->getRetailer() && $this->currentStore->getRetailer()->getId()) {
            $retailerId = $this->currentStore->getRetailer()->getId();
        }

        if ($retailerId) {
            $offer = $this->helper->getOffer($product, $retailerId);
        }

        return $offer;
    }

    /**
     * Check if we should use store offers
     *
     * @return bool
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    private function useStoreOffers()
    {
        return !($this->isAdmin() || !$this->settingsHelper->isDriveMode());
    }

    /**
     * Check if we are browsing admin area
     *
     * @return bool
     *
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    private function isAdmin()
    {
        return $this->state->getAreaCode() == \Magento\Backend\App\Area\FrontNameResolver::AREA_CODE;
    }
}
