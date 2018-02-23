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
namespace Smile\RetailerOfferGroupPrice\Plugin\Index\Mapping;

use Smile\ElasticsuiteCore\Index\Mapping\Field;

/**
 * Field plugin.
 *
 * fix remove after update to elasticsuite
 */
class FieldPlugin
{
    /**
     * Change isNested result for offer.seller_id and offer.customer_group_id field.
     *
     * @param Field $field    Field.
     * @param bool  $isNested Is nested
     *
     * @return bool
     */
    public function afterIsNested(Field $field, $isNested)
    {
        if (in_array($field->getName(), ['offer.seller_id', 'offer.customer_group_id'])) {
            $isNested = false;
        }

        return $isNested;
    }
}
