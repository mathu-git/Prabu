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
 * @package Cgi\ProductRestriction\Helper
 */
class Data extends AbstractHelper
{
    /**
     *
     */
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

    /**
     * Data constructor.
     * @param TimezoneInterface $dateTime
     * @param StoreManagerInterface $storeManager
     * @param Session $customerSession
     * @param RestrictionRule $ruleResource
     * @param Context $context
     */
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

    /**
     * @return bool
     * @throws \Magento\Framework\Exception\LocalizedException
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
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

    /**
     * @return bool
     * @throws \Magento\Framework\Exception\LocalizedException
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
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

    /**
     * @return array|bool
     * @throws \Magento\Framework\Exception\LocalizedException
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function getRestrictionProducts(){
        $restrictedId = $this->getActiveRulesId();
        $websiteId = (int)$this->storeManager->getStore()->getWebsiteId();
        $customergroupId = (int)$this->customerSession->getCustomerGroupId();
        $productIds = $this->ruleResource->getRestrectionProductIds($restrictedId, $websiteId, $customergroupId);
        if($productIds) {
            $restrictedProductIds = [];
            foreach ($productIds as $productId) {
                $restrictedProductIds[] = $productId['product_id'];
            }
            return array_unique($restrictedProductIds);
        }
        return false;
    }


}
