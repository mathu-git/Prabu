<?php
/**
 * Copyright © 2020 CGI. All rights reserved.
 * See COPYING.txt for license details.
 *
 * @author    CGI <info.de@cgi.com>
 * @copyright 2020 CGI
 * @license   http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

namespace Cgi\ProductRestriction\Helper;

use Magento\Framework\App\Helper\AbstractHelper;
use Magento\Framework\App\Helper\Context;
use Cgi\ProductRestriction\Model\Rewrite\ResourceModel\RestrictionRule;
use Magento\Customer\Model\Session;
use Magento\Framework\Stdlib\DateTime\TimezoneInterface;
use Magento\Store\Model\StoreManagerInterface;

/**
 * Class Data
 *
 * @package Cgi\ProductRestriction\Helper
 */
class Data extends AbstractHelper
{
    /**
     * Restricted product rule flag
     */
    const IS_RESTRICTION = 1;

    /**
     * Check the timezone
     *
     * @var TimezoneInterface
     */
    protected $dateTime;

    /**
     * Store Manager
     *
     * @var StoreManagerInterface
     */
    protected $storeManager;

    /**
     * Get the Customer Data
     *
     * @var Session
     */
    protected $customerSession;

    /**
     * Restriction rule condition
     *
     * @var RestrictionRule
     */
    protected $ruleResource;

    /**
     * Data constructor.
     *
     * @param TimezoneInterface     $dateTime
     * @param StoreManagerInterface $storeManager
     * @param Session               $customerSession
     * @param RestrictionRule       $ruleResource
     * @param Context               $context
     */
    public function __construct(
        TimezoneInterface $dateTime,
        StoreManagerInterface $storeManager,
        Session $customerSession,
        RestrictionRule $ruleResource,
        Context $context
    ) {
        parent::__construct($context);
        $this->dateTime = $dateTime;
        $this->storeManager = $storeManager;
        $this->customerSession = $customerSession;
        $this->ruleResource = $ruleResource;
    }

    /**
     * Using customer group id to get the rule id
     *
     * @return array|bool
     */
    public function getRestrictionCustomerGroupRuleds()
    {
        $customergroupId = (int)$this->customerSession->getCustomerGroupId();
        $ruleIds = $this->ruleResource->getRulesId($customergroupId);
        $restrictionRuleId = [];
        if ($ruleIds) {
            foreach ($ruleIds as $ruleId) {
                $restrictionRuleId[] = $ruleId['rule_id'];
            }

            return $restrictionRuleId;
        }
        return false;
    }

    /**
     * Get the Active rule id
     *
     * @return array|bool
     */
    public function getActiveRulesId()
    {

        $isRestriction = self::IS_RESTRICTION;
        $groupRuleIds = $this->getRestrictionCustomerGroupRuleds();
        $rulesId = $this->ruleResource->getRestrictionActiveIds($groupRuleIds, $isRestriction);
        if ($rulesId) {
            $restrictionsId = [];
            foreach ($rulesId as $ruleId) {
                $restrictionsId[] = $ruleId['rule_id'];
            }
            return $restrictionsId;
        }
        return false;
    }

    /**
     * Selected restricted product ids based on the rule id
     *
     * @return array|bool
     */
    public function getRestrictionProducts()
    {
        $restrictedId = $this->getActiveRulesId();
        $websiteId = (int)$this->storeManager->getStore()->getWebsiteId();
        $customergroupId = (int)$this->customerSession->getCustomerGroupId();
        $productIds = $this->ruleResource->getRestrectionProductIds($restrictedId, $websiteId, $customergroupId);
        if ($productIds) {
            $restrictedProductIds = [];
            foreach ($productIds as $productId) {
                $restrictedProductIds[] = $productId['product_id'];
            }
            return array_unique($restrictedProductIds);
        }
        return false;
    }
}
