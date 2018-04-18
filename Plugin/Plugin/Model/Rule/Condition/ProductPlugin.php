<?php
/**
 * DISCLAIMER
 * Do not edit or add to this file if you wish to upgrade this module to newer
 * versions in the future.
 *
 * @category  Smile
 * @package   Smile\RetailerOfferGroupPrice
 * @author    Maxime Leclercq <maxime.leclercq@smile.fr>
 * @copyright 2018 Smile
 * @license   Open Software License ("OSL") v. 3.0
 */
namespace Smile\RetailerOfferGroupPrice\Plugin\Plugin\Model\Rule\Condition;

use Smile\ElasticsuiteCore\Search\Request\QueryInterface;
use Smile\RetailerOffer\Plugin\Model\Rule\Condition\ProductPlugin as BaseProductPlugin;
use Smile\ElasticsuiteCore\Search\Request\Query\QueryFactory;
use Magento\Customer\Model\Session as CustomerSession;

/**
 * Product plugin for offer price condition.
 *
 * @category Smile
 * @package  Smile\RetailerOfferGroupPrice
 * @author   Maxime Leclercq <maxime.leclercq@smile.fr>
 */
class ProductPlugin
{
    /**
     * Query factory.
     *
     * @var QueryFactory
     */
    private $queryFactory;

    /**
     * Customer session.
     *
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
     * Add customer group filter.
     *
     * @param BaseProductPlugin $source      Product plugin instance.
     * @param array             $mustClauses Must clauses.
     *
     * @return array
     * @SuppressWarnings(PHPMD.UnusedFormalParameters)
     */
    public function afterGetStoreLimitationMustClauses(BaseProductPlugin $source, array $mustClauses)
    {
        $customerGroupIdFilter = $this->queryFactory->create(
            QueryInterface::TYPE_TERM,
            ['field' => 'offer.customer_group_id', 'value' => $this->customerSession->getCustomerGroupId()]
        );
        $mustClauses[] = $customerGroupIdFilter;

        return $mustClauses;
    }
}
