<?php
/**
 * Copyright © 2020 CGI. All rights reserved.
 * See COPYING.txt for license details.
 *
 * @author    CGI <info.de@cgi.com>
 * @copyright 2020 CGI
 * @license   http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

namespace Cgi\ProductRestriction\Api;

use Cgi\ProductRestriction\Api\Data\RuleInterface;

/**
 * Interface RestrictionRuleRepositoryInterface
 * @api
 * @since 100.1.0
 */
interface RestrictionRuleRepositoryInterface
{
    /**
     * @param RuleInterface $rule
     * @return RuleInterface
     * @throws \Magento\Framework\Exception\CouldNotSaveException
     * @since 100.1.0
     */
    public function save(RuleInterface $rule);

    /**
     * @param int $ruleId
     * @return RuleInterface
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     * @since 100.1.0
     */
    public function get($ruleId);

    /**
     * @param RuleInterface $rule
     * @return bool
     * @throws \Magento\Framework\Exception\CouldNotDeleteException
     * @since 100.1.0
     */
    public function delete(RuleInterface $rule);

    /**
     * @param int $ruleId
     * @return bool
     * @throws \Magento\Framework\Exception\CouldNotDeleteException
     * @since 100.1.0
     */
    public function deleteById($ruleId);
}
