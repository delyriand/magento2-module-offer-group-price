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
namespace Smile\RetailerOfferGroupPrice\Plugin\Model;

use Smile\RetailerOfferGroupPrice\Model\CollectionProcessor;
use Smile\ElasticsuiteCore\Search\Request\QueryInterface;
use Smile\ElasticsuiteCore\Search\Request\Query\QueryFactory;
use Magento\Customer\Model\Session as CustomerSession;

/**
 * Collection processor plugin.
 */
class CollectionProcessorPlugin
{
    /**
     * @var QueryFactory
     */
    private $queryFactory;

    /**
     * @var CustomerSession
     */
    private $customerSession;

    /**
     * CollectionProcessorPlugin constructor.
     *
     * @param QueryFactory    $queryFactory    Query factory.
     * @param CustomerSession $customerSession Customer session.
     */
    public function __construct(QueryFactory $queryFactory, CustomerSession $customerSession)
    {
        $this->queryFactory = $queryFactory;
        $this->customerSession = $customerSession;
    }

    /**
     * Add is_in_stock filter in stock contions.
     *
     * @param CollectionProcessor $collectionProcessor Collection processor.
     * @param array               $mustClauses         Must clauses.
     *
     * @return array
     * @SuppressWarnings(PHPMD.UnusedFormalParameters)
     */
    public function afterGetStockMustClauses(CollectionProcessor $collectionProcessor, array $mustClauses)
    {
        $isInStockFilter = $this->queryFactory->create(
            QueryInterface::TYPE_TERM,
            ['field' => 'offer.is_in_stock', 'value' => true]
        );
        $mustClauses[] = $isInStockFilter;

        return $mustClauses;
    }

    /**
     * Add customer_group_id filter in store limitation conditions.
     *
     * @param CollectionProcessor $collectionProcessor Collection processor.
     * @param array               $mustClauses         Must clauses.
     *
     * @return array
     * @SuppressWarnings(PHPMD.UnusedFormalParameters)
     */
    public function afterGetStoreLimitationMustClauses(CollectionProcessor $collectionProcessor, array $mustClauses)
    {
        $customerGroupIdFilter = $this->queryFactory->create(
            QueryInterface::TYPE_TERM,
            ['field' => 'offer.customer_group_id', 'value' => $this->customerSession->getCustomerGroupId()]
        );
        $mustClauses[] = $customerGroupIdFilter;

        return $mustClauses;
    }
}
