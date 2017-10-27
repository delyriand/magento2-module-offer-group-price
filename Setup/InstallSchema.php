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
namespace Smile\RetailerOfferGroupPrice\Setup;

use Magento\Framework\DB\Adapter\AdapterInterface;
use Magento\Framework\Setup\InstallSchemaInterface;
use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\Setup\SchemaSetupInterface;
use Smile\Offer\Api\Data\OfferInterface;
use Smile\RetailerOfferGroupPrice\Api\Data\OfferGroupPriceInterface;

/**
 * Install schema.
 */
class InstallSchema implements InstallSchemaInterface
{
    /**
     * Installs DB schema for a module
     *
     * @param SchemaSetupInterface   $setup   Setup.
     * @param ModuleContextInterface $context Context.
     *
     * @return void
     * @throws \Zend_Db_Exception
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function install(SchemaSetupInterface $setup, ModuleContextInterface $context)
    {
        $table = $setup->getConnection()
            ->newTable($setup->getTable(OfferGroupPriceInterface::TABLE_NAME))
            ->addColumn(
                OfferGroupPriceInterface::FIELD_ID,
                \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
                null,
                ['identity' => true, 'unsigned' => true, 'nullable' => false, 'primary' => true],
                'Offer Group Price ID'
            )
            ->addColumn(
                OfferGroupPriceInterface::FIELD_OFFER_ID,
                \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
                null,
                ['unsigned' => true, 'nullable' => false],
                'Offer ID'
            )
            ->addColumn(
                OfferGroupPriceInterface::FIELD_CUSTOMER_GROUP_ID,
                \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
                null,
                ['unsigned' => true, 'nullable' => false],
                'Customer group ID'
            )
            ->addColumn(
                OfferGroupPriceInterface::FIELD_PRICE,
                \Magento\Framework\DB\Ddl\Table::TYPE_DECIMAL,
                '12,4',
                ['nullable' => true],
                'Offer price'
            )
            ->addColumn(
                OfferGroupPriceInterface::FIELD_SPECIAL_PRICE,
                \Magento\Framework\DB\Ddl\Table::TYPE_DECIMAL,
                '12,4',
                ['nullable' => true],
                'Offer special price'
            )
            ->addIndex(
                $setup->getIdxName(
                    OfferGroupPriceInterface::TABLE_NAME,
                    [OfferGroupPriceInterface::FIELD_OFFER_ID, OfferGroupPriceInterface::FIELD_CUSTOMER_GROUP_ID],
                    AdapterInterface::INDEX_TYPE_UNIQUE
                ),
                [OfferGroupPriceInterface::FIELD_OFFER_ID, OfferGroupPriceInterface::FIELD_CUSTOMER_GROUP_ID],
                ['type' => AdapterInterface::INDEX_TYPE_UNIQUE]
            )
            ->addForeignKey(
                $setup->getFkName(
                    OfferGroupPriceInterface::TABLE_NAME,
                    OfferGroupPriceInterface::FIELD_OFFER_ID,
                    'smile_offer',
                    OfferInterface::OFFER_ID
                ),
                OfferGroupPriceInterface::FIELD_OFFER_ID,
                $setup->getTable('smile_offer'),
                OfferInterface::OFFER_ID,
                \Magento\Framework\DB\Ddl\Table::ACTION_CASCADE
            )
            ->addForeignKey(
                $setup->getFkName(
                    OfferGroupPriceInterface::TABLE_NAME,
                    OfferGroupPriceInterface::FIELD_CUSTOMER_GROUP_ID,
                    'customer_group',
                    'customer_group_id'
                ),
                OfferGroupPriceInterface::FIELD_CUSTOMER_GROUP_ID,
                $setup->getTable('customer_group'),
                'customer_group_id',
                \Magento\Framework\DB\Ddl\Table::ACTION_CASCADE
            )
        ;

        $setup->getConnection()->createTable($table);
    }
}
