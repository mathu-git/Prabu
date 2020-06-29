<?php
/**
 * *
 *  * Copyright © 2020 CGI. All rights reserved.
 *  * See COPYING.txt for license details.
 *  *
 *  * @author    CGI <info.de@cgi.com>
 *  * @copyright 2020 CGI
 *  * @license   http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 *
 */

namespace Cgi\ProductRestriction\Api\Data;

use Magento\Framework\Api\CustomAttributesDataInterface;

interface RuleInterface extends CustomAttributesDataInterface
{
    const RULE_ID = 'rule_id';

    const NAME = 'name';

    const DESCRIPTION = 'description';

    const IS_ACTIVE = 'is_active';

    const STOP_RULES_PROCESSING = 'stop_rules_processing';

    const SORT_ORDER = 'sort_order';

    const SIMPLE_ACTION = 'simple_action';

    const DISCOUNT_AMOUNT = 'discount_amount';

    /**
     * Returns rule id field
     *
     * @return int|null
     * @since 100.1.0
     */
    public function getRuleId();

    /**
     * @param int $ruleId
     * @return $this
     * @since 100.1.0
     */
    public function setRuleId($ruleId);

    /**
     * Returns rule name
     *
     * @return string
     * @since 100.1.0
     */
    public function getName();

    /**
     * @param string $name
     * @return $this
     * @since 100.1.0
     */
    public function setName($name);

    /**
     * Returns rule description
     *
     * @return string|null
     * @since 100.1.0
     */
    public function getDescription();

    /**
     * @param string $description
     * @return $this
     * @since 100.1.0
     */
    public function setDescription($description);

    /**
     * Returns rule activity flag
     *
     * @return int
     * @since 100.1.0
     */
    public function getIsActive();

    /**
     * @param int $isActive
     * @return $this
     * @since 100.1.0
     */
    public function setIsActive($isActive);

    /**
     * Returns rule condition
     *
     * @return \Cgi\ProductRestriction\Api\Data\ConditionInterface|null
     * @since 100.1.0
     */
    public function getRuleCondition();

    /**
     * @param \Cgi\ProductRestriction\Api\Data\ConditionInterface $condition
     * @return $this
     * @since 100.1.0
     */
    public function setRuleCondition($condition);

    /**
     * Returns stop rule processing flag
     *
     * @return int|null
     * @since 100.1.0
     */
    public function getStopRulesProcessing();

    /**
     * @param int $isStopProcessing
     * @return $this
     * @since 100.1.0
     */
    public function setStopRulesProcessing($isStopProcessing);

    /**
     * Returns rule sort order
     *
     * @return int|null
     * @since 100.1.0
     */
    public function getSortOrder();

    /**
     * @param int $sortOrder
     * @return $this
     * @since 100.1.0
     */
    public function setSortOrder($sortOrder);

    /**
     * Returns rule simple action
     *
     * @return string
     * @since 100.1.0
     */
    public function getSimpleAction();

    /**
     * @param string $action
     * @return $this
     * @since 100.1.0
     */
    public function setSimpleAction($action);

    public function getConditionsFieldSetId(string $formName);

    /**
     * Returns discount amount
     *
     * @return float
     * @since 100.1.0
     */
    public function getDiscountAmount();

    /**
     * @param float $amount
     * @return $this
     * @since 100.1.0
     */
    public function setDiscountAmount($amount);
}

