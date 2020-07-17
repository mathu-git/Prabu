<?php
/**
 * Copyright © 2020 CGI. All rights reserved.
 * See COPYING.txt for license details.
 *
 * @author    CGI <info.de@cgi.com>
 * @copyright 2020 CGI
 * @license   http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * Catalog rules resource model
 */

namespace Cgi\ProductRestriction\Model\Rewrite\ResourceModel;

use Magento\CatalogRule\Model\ResourceModel\Rule;

/**
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
 */
class RestrictionRule extends Rule
{
    /**
     * Catalog Customer Group Table name
     */
    const CATALOGRULE_CUSTOMER_GROUP = 'catalogrule_customer_group';

    /**
     * Catalog rule table name
     */
    const CATALOGRULE_TABLE = 'catalogrule';

    /**
     * Mapping catalog rule product table name
     */
    const RESTRICTION_PRODUCT_TABLE = 'catalogrule_product';
    /**
     * Get the Rules id using Customer group id
     *
     * @param  $gId
     * @return array
     */
    public function getRulesId($gId)
    {

        $connection = $this->getConnection();
        $select = $connection->select()
            ->from($this->getTable(self::CATALOGRULE_CUSTOMER_GROUP), ['customer_group_id', 'rule_id'])
            ->where('customer_group_id = ?', $gId);
        return $connection->fetchAll($select);
    }

    /**
     * Using rule id and restriction flat to get the particular rule id
     *
     * @param  $restrictonRuleId
     * @param  $isRestriction
     * @return array
     */
    public function getRestrictionActiveIds($restrictonRuleId, $isRestriction)
    {
        $connection = $this->getConnection();
        $select = $connection->select()
            ->from($this->getTable(self::CATALOGRULE_TABLE), ['rule_id'])
            ->where('is_product_restriction = ?', $isRestriction)
            ->where('is_active = ?', 1)
            ->where('rule_id IN(?)', $restrictonRuleId);

        return $connection->fetchAll($select);
    }

    /**
     * Get restriction product id using created rule id
     *
     * @param  $restrictedId
     * @param  $websiteId
     * @param  $customergroupId
     * @return array
     */
    public function getRestrectionProductIds($restrictedId, $websiteId, $customergroupId)
    {
        $connection = $this->getConnection();
        $select = $connection->select()
            ->from($this->getTable(self::RESTRICTION_PRODUCT_TABLE), ['rule_id', 'product_id'])
            ->where('customer_group_id = ?', $customergroupId)
            ->where('website_id = ?', $websiteId)
            ->where('rule_id IN(?)', $restrictedId);
        return $connection->fetchAll($select);
    }
}
