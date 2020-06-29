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
use Magento\Catalog\Model\Product;
use Cgi\ProductRestriction\Model\Rewrite\ResourceModel\RestrictionRule;
use Magento\Customer\Model\Session;
use Magento\Framework\Stdlib\DateTime\TimezoneInterface;
use Magento\Store\Model\StoreManagerInterface;

class Data extends AbstractHelper
{
    const RESTRICTION_CODE = 'catalogrule_product';

    const IS_RESTRICTION = 1;

    /**
     * @var TimezoneInterface
     */
    protected $dateTime;

    /**
     * @var StoreManagerInterface
     */
    protected $storeManager;

    /**
     * @var Session
     */
    protected $customerSession;

    /**
     * @var Rule
     */
    //private $ruleResource;

    public function __construct(
        TimezoneInterface $dateTime,
        StoreManagerInterface $storeManager,
        Session $customerSession,
        RestrictionRule $ruleResource,
        Context $context)
    {
        parent::__construct($context);
        $this->dateTime = $dateTime;
        $this->storeManager = $storeManager;
        $this->customerSession = $customerSession;
        $this->ruleResource = $ruleResource;
    }

    public function getRestrictionCustomerGroupRuleds(){

        $dateTime = $this->dateTime->scopeDate($this->storeManager->getStore()->getId());
        $websiteId = (int)$this->storeManager->getStore()->getWebsiteId();
        $customergroupId = (int)$this->customerSession->getCustomerGroupId();
        $ruleIds = $this->ruleResource->getRulesId($customergroupId);
        if($ruleIds) {
            foreach ($ruleIds as $ruleId) {
                $restrictonRuleId[] = $ruleId['rule_id'];
            }
            return $restrictonRuleId;
        }
        return false;

    }

    public function getActiveRulesId(){

        $dateTime = $this->dateTime->scopeDate($this->storeManager->getStore()->getId());
        $isRestriction = self::IS_RESTRICTION;
        $groupRuleIds = $this->getRestrictionCustomerGroupRuleds();
        $rulesId = $this->ruleResource->getRestrictionActiveIds($groupRuleIds, $isRestriction);
        if($rulesId) {
            foreach ($rulesId as $ruleId) {
                $restrictionsId[] = $ruleId['rule_id'];
            }
            return $restrictionsId;
        }
        return false;
    }

    public function getRestrictionProducts(){
        $restrictedId = $this->getActiveRulesId();
        $websiteId = (int)$this->storeManager->getStore()->getWebsiteId();
        $customergroupId = (int)$this->customerSession->getCustomerGroupId();
        $productIds = $this->ruleResource->getRestrectionProductIds($restrictedId, $websiteId, $customergroupId);
        if($productIds) {
            foreach ($productIds as $productId) {
                $restrictedProductIds[] = $productId['product_id'];
            }
            return array_unique($restrictedProductIds);
        }
        return false;
    }


}
