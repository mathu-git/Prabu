<?php
/**
 * Copyright © 2020 CGI. All rights reserved.
 * See COPYING.txt for license details.
 *
 * @author    CGI <info.de@cgi.com>
 * @copyright 2020 CGI
 * @license   http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

namespace Cgi\ProductRestriction\Model;

use Cgi\ProductRestriction\Api\Data\RuleInterface;
use Cgi\ProductRestriction\Api\RestrictionRuleRepositoryInterface;
use Magento\Framework\Exception\CouldNotDeleteException;
use Magento\Framework\Exception\CouldNotSaveException;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\Exception\ValidatorException;

/**
 * Class RestrictionRuleRepository
 * @package Cgi\ProductRestriction\Model
 */
class RestrictionRuleRepository implements RestrictionRuleRepositoryInterface
{
    /**
     * @var ResourceModel\Rule
     */
    protected $ruleResource;

    /**
     * @var RuleFactory
     */
    protected $ruleFactory;

    /**
     * @var array
     */
    private $rules = [];

    /**
     * RestrictionRuleRepository constructor.
     * @param ResourceModel\Rule $ruleResource
     * @param RuleFactory $ruleFactory
     */
    public function __construct(
        \Cgi\ProductRestriction\Model\ResourceModel\Rule $ruleResource,
    \Cgi\ProductRestriction\Model\RuleFactory $ruleFactory
    )
    {

        $this->ruleResource = $ruleResource;
        $this->ruleFactory = $ruleFactory;
    }

    /**
     * @param RuleInterface $rule
     * @return RuleInterface|mixed
     * @throws CouldNotSaveException
     * @throws NoSuchEntityException
     */
    public function save(RuleInterface $rule)
    {
        if ($rule->getRuleId()) {
            $rule = $this->get($rule->getRuleId())->addData($rule->getData());
        }


        try {
            $this->ruleResource->save($rule);
            unset($this->rules[$rule->getId()]);
        } catch (ValidatorException $e) {
            throw new CouldNotSaveException(__($e->getMessage()));
        } catch ( \Exception $e ) {
            throw new CouldNotSaveException(
                __('The "%1" rule was unable to be saved. Please try again.', $rule->getRuleId())
            );
        }
        return $rule;
    }

    /**
     * @param int $ruleId
     * @return RuleInterface|mixed
     * @throws NoSuchEntityException
     */
    public function get($ruleId)
    {
        if (!isset($this->rules[$ruleId])) {
            $rule = $this->ruleFactory->create();

            /* TODO: change to resource model after entity manager will be fixed */
            $rule->load($ruleId);
            if (!$rule->getRuleId()) {
                throw new NoSuchEntityException(
                    __('The rule with the "%1" ID wasn\'t found. Verify the ID and try again.', $ruleId)
                );
            }
            $this->rules[$ruleId] = $rule;
        }
        return $this->rules[$ruleId];
    }

    /**
     * @param RuleInterface $rule
     * @return bool
     * @throws CouldNotDeleteException
     * @throws CouldNotSaveException
     */
    public function delete(RuleInterface $rule)
    {
        try {
            $this->ruleResource->delete($rule);
            unset($this->rules[$rule->getId()]);
        } catch (ValidatorException $e) {
            throw new CouldNotSaveException(__($e->getMessage()));
        } catch (\Exception $e) {
            throw new CouldNotDeleteException(__('The "%1" rule couldn\'t be removed.', $rule->getRuleId()));
        }
        return true;
    }

    /**
     * @param int $ruleId
     * @return bool
     * @throws CouldNotDeleteException
     * @throws CouldNotSaveException
     * @throws NoSuchEntityException
     */
    public function deleteById($ruleId)
    {
        $model = $this->get($ruleId);
        $this->delete($model);
        return true;
    }
}
