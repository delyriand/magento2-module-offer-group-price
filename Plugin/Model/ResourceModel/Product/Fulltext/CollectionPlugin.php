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
namespace Smile\RetailerOfferGroupPrice\Plugin\Model\ResourceModel\Product\Fulltext;

use Smile\ElasticsuiteCatalog\Model\ResourceModel\Product\Fulltext\Collection;
use Magento\Customer\Model\Session as CustomerSession;
use Smile\ElasticsuiteCore\Search\Request\QueryInterface;

/**
 * Product fulltext collection.
 */
class CollectionPlugin
{
    /**
     * @var CustomerSession
     */
    private $customerSession;

    /**
     * CollectionPlugin constructor.
     *
     * @param CustomerSession $customerSession Customer session.
     */
    public function __construct(CustomerSession $customerSession)
    {
        $this->customerSession = $customerSession;
    }

    /**
     * Append customer_group_id nested filter.
     *
     * @param Collection  $collection   Products collection.
     * @param string      $sortName     Sort name
     * @param string      $sortField    Sort field name.
     * @param string|null $nestedPath   Nested path
     * @param array|null  $nestedFilter Nested filters.
     *
     * @return array
     * @SuppressWarnings(PHPMD.UnusedFormalParameters)
     */
    public function beforeAddSortFilterParameters(Collection $collection, $sortName, $sortField, $nestedPath = null, $nestedFilter = null)
    {
        if ($sortName == 'price' && $sortField == 'offer.price') {
            $nestedFilter['offer.customer_group_id'] = $this->customerSession->getCustomerGroupId();
        }

        return [$sortName, $sortField, $nestedPath, $nestedFilter];
    }

    /**
     * Append customer_group_id nested filter.
     *
     * @param Collection $collection  Collection.
     * @param string     $field       Field name.
     * @param string     $facetType   Field type name.
     * @param array      $facetConfig Facet configuration.
     * @param array|null $facetFilter Facet filter.
     *
     * @return array
     * @SuppressWarnings(PHPMD.UnusedFormalParameters)
     */
    public function beforeAddFacet(Collection $collection, $field, $facetType, $facetConfig, $facetFilter = null)
    {
        if ($field == 'offer.price') {
            $facetConfig['nestedFilter']['offer.customer_group_id'] = $this->customerSession->getCustomerGroupId();
        }

        return [$field, $facetType, $facetConfig, $facetFilter];
    }
}
