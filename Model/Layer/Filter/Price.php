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
namespace Smile\RetailerOfferGroupPrice\Model\Layer\Filter;

use Smile\ElasticsuiteCore\Search\Request\QueryInterface;

/**
 * Filter price model
 *
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
 */
class Price extends \Smile\RetailerOffer\Model\Layer\Filter\Price
{
    /**
     * @var \Smile\RetailerOffer\Helper\Settings
     */
    private $settingsHelper;

    /**
     * @var \Magento\Catalog\Model\Layer\Filter\DataProvider\Price
     */
    private $dataProvider;

    /**
     * @var \Smile\StoreLocator\CustomerData\CurrentStore
     */
    private $currentStore;

    /**
     * @var \Smile\ElasticsuiteCore\Search\Request\Query\QueryFactory
     */
    private $queryFactory;

    /**
     * @var \Magento\Customer\Model\Session
     */
    private $customerSession;

    /**
     * Constructor.
     *
     * @SuppressWarnings(PHPMD.ExcessiveParameterList)
     *
     * @param \Magento\Catalog\Model\Layer\Filter\ItemFactory               $filterItemFactory   Item filter factory.
     * @param \Magento\Store\Model\StoreManagerInterface                    $storeManager        Store manager.
     * @param \Magento\Catalog\Model\Layer                                  $layer               Search layer.
     * @param \Magento\Catalog\Model\Layer\Filter\Item\DataBuilder          $itemDataBuilder     Item data builder.
     * @param \Magento\Catalog\Model\ResourceModel\Layer\Filter\Price       $resource            Price resource.
     * @param \Magento\Customer\Model\Session                               $customerSession     Customer session.
     * @param \Magento\Framework\Search\Dynamic\Algorithm                   $priceAlgorithm      Price algorithm.
     * @param \Magento\Framework\Pricing\PriceCurrencyInterface             $priceCurrency       Price currency.
     * @param \Magento\Catalog\Model\Layer\Filter\Dynamic\AlgorithmFactory  $algorithmFactory    Algorithm factory.
     * @param \Magento\Catalog\Model\Layer\Filter\DataProvider\PriceFactory $dataProviderFactory Data provider.
     * @param \Smile\RetailerOffer\Helper\Settings                          $settingsHelper      Settings Helper.
     * @param \Smile\StoreLocator\CustomerData\CurrentStore                 $currentStore        Current Store.
     * @param \Smile\ElasticsuiteCore\Search\Request\Query\QueryFactory     $queryFactory        Query Factory.
     * @param array                                                         $data                Custom data.
     */
    public function __construct(
        \Magento\Catalog\Model\Layer\Filter\ItemFactory $filterItemFactory,
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        \Magento\Catalog\Model\Layer $layer,
        \Magento\Catalog\Model\Layer\Filter\Item\DataBuilder $itemDataBuilder,
        \Magento\Catalog\Model\ResourceModel\Layer\Filter\Price $resource,
        \Magento\Customer\Model\Session $customerSession,
        \Magento\Framework\Search\Dynamic\Algorithm $priceAlgorithm,
        \Magento\Framework\Pricing\PriceCurrencyInterface $priceCurrency,
        \Magento\Catalog\Model\Layer\Filter\Dynamic\AlgorithmFactory $algorithmFactory,
        \Magento\Catalog\Model\Layer\Filter\DataProvider\PriceFactory $dataProviderFactory,
        \Smile\RetailerOffer\Helper\Settings $settingsHelper,
        \Smile\StoreLocator\CustomerData\CurrentStore $currentStore,
        \Smile\ElasticsuiteCore\Search\Request\Query\QueryFactory $queryFactory,
        array $data = []
    ) {
        parent::__construct(
            $filterItemFactory,
            $storeManager,
            $layer,
            $itemDataBuilder,
            $resource,
            $customerSession,
            $priceAlgorithm,
            $priceCurrency,
            $algorithmFactory,
            $dataProviderFactory,
            $settingsHelper,
            $currentStore,
            $queryFactory,
            $data
        );

        $this->customerSession = $customerSession;
        $this->settingsHelper = $settingsHelper;
        $this->dataProvider   = $dataProviderFactory->create(['layer' => $this->getLayer()]);
        $this->currentStore   = $currentStore;
        $this->queryFactory   = $queryFactory;
    }

    /**
     * {@inheritdoc}
     */
    public function apply(\Magento\Framework\App\RequestInterface $request)
    {
        if (!$this->getRetailerId() || !$this->settingsHelper->useStoreOffers()) {
            return parent::apply($request);
        }

        $filter = $request->getParam($this->getRequestVar());
        if ($filter && !is_array($filter)) {
            $filterParams = explode(',', $filter);
            $filter = $this->dataProvider->validateFilter($filterParams[0]);

            if ($filter) {
                $this->dataProvider->setInterval($filter);
                $priorFilters = $this->dataProvider->getPriorFilters($filterParams);
                if ($priorFilters) {
                    $this->dataProvider->setPriorIntervals($priorFilters);
                }

                list($fromValue, $toValue) = $filter;
                $this->setCurrentValue(['from' => $fromValue, 'to' => $toValue]);

                $this->addQueryFilter($fromValue, $toValue);

                $this->getLayer()->getState()->addFilter(
                    $this->_createItem($this->_renderRangeLabel(empty($fromValue) ? 0 : $fromValue, $toValue), $filter)
                );
            }
        }

        return $this;
    }

    /**
     * Retrieve current retailer Id.
     *
     * @return int|null
     */
    private function getRetailerId()
    {
        $retailerId = null;
        if ($this->currentStore->getRetailer() && $this->currentStore->getRetailer()->getId()) {
            $retailerId = (int) $this->currentStore->getRetailer()->getId();
        }

        return $retailerId;
    }

    /**
     * Compute proper price interval for current Retailer.
     *
     * @param int $fromValue The From value for price interval
     * @param int $toValue   The To value for price interval
     */
    private function addQueryFilter($fromValue, $toValue)
    {
        $sellerIdFilter = $this->queryFactory->create(
            QueryInterface::TYPE_TERM,
            ['field' => 'offer.seller_id', 'value' => $this->getRetailerId()]
        );

        $customerGroupIdFilter = $this->queryFactory->create(
            QueryInterface::TYPE_TERM,
            ['field' => 'offer.customer_group_id', 'value' => $this->customerSession->getCustomerGroupId()]
        );
        $mustClause  = ['must' => [$sellerIdFilter, $customerGroupIdFilter]];

        $rangeFilter = $this->queryFactory->create(
            QueryInterface::TYPE_RANGE,
            ['field' => 'offer.price', 'bounds' => ['gte' => $fromValue, 'lte' => $toValue]]
        );
        $mustClause['must'][] = $rangeFilter;

        $boolFilter   = $this->queryFactory->create(QueryInterface::TYPE_BOOL, $mustClause);
        $nestedFilter = $this->queryFactory->create(QueryInterface::TYPE_NESTED, ['path' => 'offer', 'query' => $boolFilter]);

        $this->getLayer()->getProductCollection()->addQueryFilter($nestedFilter);
    }
}
