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
namespace Smile\RetailerOfferGroupPrice\Model\ResourceModel;

use Magento\Framework\Model\ResourceModel\Db\AbstractDb;
use Magento\Framework\EntityManager\EntityManager;
use Magento\Framework\EntityManager\MetadataPool;
use Magento\Framework\Model\ResourceModel\Db\Context;
use Smile\RetailerOfferGroupPrice\Api\Data\OfferGroupPriceInterface;
use Magento\Framework\Model\AbstractModel;
use Magento\Framework\DB\Select;

/**
 * Resource model for customer group price.
 */
class OfferGroupPrice extends AbstractDb
{
    /**
     * @var EntityManager
     */
    private $entityManager;

    /**
     * @var MetadataPool
     */
    private $metadataPool;

    /**
     * Class constructor
     *
     * @param Context       $context        Context
     * @param EntityManager $entityManager  Entity Manager
     * @param MetadataPool  $metadataPool   Metadata pool
     * @param string        $connectionName Connection name
     */
    public function __construct(
        Context       $context,
        EntityManager $entityManager,
        MetadataPool  $metadataPool,
        $connectionName = null
    ) {
        parent::__construct($context, $connectionName);

        $this->entityManager = $entityManager;
        $this->metadataPool  = $metadataPool;
    }

    /**
     * Get connection
     *
     * @return \Magento\Framework\DB\Adapter\AdapterInterface|false
     * @throws \Exception
     */
    public function getConnection()
    {
        return $this->_resources->getConnection(
            $this->metadataPool->getMetadata(OfferGroupPriceInterface::class)->getEntityConnectionName()
        );
    }

    /**
     * Load an object
     *
     * @param AbstractModel $object Object
     * @param mixed         $value  Value
     * @param string        $field  Field
     *
     * @return $this
     * @throws \Exception
     */
    public function load(AbstractModel $object, $value, $field = null)
    {
        $objectId = $this->getObjectId($value, $field);

        if ($objectId) {
            $this->entityManager->load($object, $objectId);
        }

        return $this;
    }

    /**
     * Save an object
     *
     * @param AbstractModel $object Object
     *
     * @return $this
     * @throws \Exception
     */
    public function save(AbstractModel $object)
    {
        $this->entityManager->save($object);

        return $this;
    }

    /**
     * Delete an object
     *
     * @param AbstractModel $object Object
     *
     * @return $this
     * @throws \Exception
     */
    public function delete(AbstractModel $object)
    {
        $this->entityManager->delete($object);

        return $this;
    }

    /**
     * Resource initialization
     *
     * @return void
     * @SuppressWarnings(PHPMD.CamelCaseMethodName)
     */
    protected function _construct()
    {
        $this->_init(OfferGroupPriceInterface::TABLE_NAME, OfferGroupPriceInterface::FIELD_ID);
    }

    /**
     * Get the id of an object with all table field
     *
     * @param mixed $value Value
     * @param null  $field Field
     *
     * @return bool|int|string
     * @throws \Exception
     */
    private function getObjectId($value, $field = null)
    {
        $entityMetadata = $this->metadataPool->getMetadata(OfferGroupPriceInterface::class);
        if ($field === null) {
            $field = $entityMetadata->getIdentifierField();
        }
        $entityId = $value;

        if ($field != $entityMetadata->getIdentifierField()) {
            $field = $this->getConnection()->quoteIdentifier(sprintf('%s.%s', $this->getMainTable(), $field));
            $select = $this->getConnection()->select()->from($this->getMainTable())->where($field . '=?', $value);

            $select->reset(Select::COLUMNS)
                ->columns($this->getMainTable() . '.' . $entityMetadata->getIdentifierField())
                ->limit(1);
            $result = $this->getConnection()->fetchCol($select);
            $entityId = count($result) ? $result[0] : false;
        }

        return $entityId;
    }
}
