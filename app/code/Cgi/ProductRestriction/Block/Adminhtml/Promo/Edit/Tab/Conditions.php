<?php
/**
 * Copyright © 2020 CGI. All rights reserved.
 * See COPYING.txt for license details.
 *
 * @author    CGI <info.de@cgi.com>
 * @copyright 2020 CGI
 * @license   http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

namespace Cgi\ProductRestriction\Block\Adminhtml\Promo\Edit\Tab;

use Magento\Backend\Block\Template\Context;
use Magento\Backend\Block\Widget\Form;
use Magento\Backend\Block\Widget\Form\Generic;
use Magento\Backend\Block\Widget\Form\Renderer\Fieldset;
use Magento\Framework\Data\FormFactory;
use Magento\Framework\Registry;
use Magento\Ui\Component\Layout\Tabs\TabInterface;
use Magento\Rule\Model\Condition\AbstractCondition;

/**
 * Class Conditions
 *
 * @package Cgi\ProductRestriction\Block\Adminhtml\Promo\Edit\Tab
 */
class Conditions extends Generic implements TabInterface
{
    /**
     * Get Render the form fieldset
     *
     * @var \Magento\Backend\Block\Widget\Form\Renderer\Fieldset
     */
    protected $_rendererFieldset;

    /**
     * Condition
     *
     * @var \Magento\Rule\Block\Conditions
     */
    protected $_conditions;

    /**
     * Passing the class in the constructor
     *
     * @param Context                        $context
     * @param Registry                       $registry
     * @param FormFactory                    $formFactory
     * @param \Magento\Rule\Block\Conditions $conditions
     * @param Fieldset                       $rendererFieldset
     * @param array                          $data
     */
    public function __construct(
        Context $context,
        Registry $registry,
        FormFactory $formFactory,
        \Magento\Rule\Block\Conditions $conditions,
        Fieldset $rendererFieldset,
        array $data = []
    ) {
        $this->_rendererFieldset = $rendererFieldset;
        $this->_conditions = $conditions;
        parent::__construct($context, $registry, $formFactory, $data);
    }

    /**
     * Get condition Label
     *
     * @return \Magento\Framework\Phrase|string
     */
    public function getTabLabel()
    {
        return __('Conditions');
    }

    /**
     * Get Table Title
     *
     * @return \Magento\Framework\Phrase|string
     */
    public function getTabTitle()
    {
        return __('Conditions');
    }

    /**
     * Admin Tab Class
     *
     * @return string|null
     */
    public function getTabClass()
    {
        return null;
    }

    /**
     * Tab Url
     *
     * @return string|null
     */
    public function getTabUrl()
    {
        return null;
    }

    /**
     * Ajax Load
     *
     * @return bool
     */
    public function isAjaxLoaded()
    {
        return false;
    }

    /**
     * Show the tab
     *
     * @return bool
     */
    public function canShowTab()
    {
        return true;
    }

    /**
     * hidden the form fields
     *
     * @return bool
     */
    public function isHidden()
    {
        return false;
    }

    /**
     * Create tab and form
     *
     * @return Form
     */
    protected function _prepareForm()
    {
        $model = $this->_coreRegistry->registry('current_promo_productionrestriction_rule');

        /**
 * @var \Magento\Framework\Data\Form $form
*/
        $form = $this->addTabToForm($model);
        $this->setForm($form);

        return parent::_prepareForm();
    }

    /**
     * Add the tab form fieldset
     *
     * @param  \Magento\CatalogRule\Api\Data\RuleInterface $model
     * @param  string                                      $fieldsetId
     * @param  string                                      $formName
     * @return \Magento\Framework\Data\Form
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    protected function addTabToForm($model, $fieldsetId = 'conditions_fieldset', $formName = 'productrestriction_rule_form')
    {
        /**
 * @var \Magento\Framework\Data\Form $form
*/
        $form = $this->_formFactory->create();
        $form->setHtmlIdPrefix('rule_');
        $conditionsFieldSetId = $model->getConditionsFieldSetId($formName);

        $newChildUrl = $this->getUrl(
            'catalog_productrestriction/promo/newConditionHtml/form/' . $conditionsFieldSetId,
            ['form_namespace' => $formName]
        );

        $renderer = $this->_rendererFieldset->setTemplate('Cgi_ProductRestriction::promo/fieldset.phtml')
            ->setNewChildUrl($newChildUrl)
            ->setFieldSetId($conditionsFieldSetId);

        $fieldset = $form->addFieldset(
            $fieldsetId,
            ['legend' => __('Conditions (don\'t add conditions if rule is applied to all products)')]
        )->setRenderer($renderer);

        $fieldset->addField(
            'conditions',
            'text',
            [
                'name' => 'conditions',
                'label' => __('Conditions'),
                'title' => __('Conditions'),
                'required' => true,
                'data-form-part' => $formName
            ]
        )
            ->setRule($model)
            ->setRenderer($this->_conditions);

        $form->setValues($model->getData());
        $this->setConditionFormName($model->getConditions(), $formName, $conditionsFieldSetId);
        return $form;
    }

    /**
     * Set the condition based on the rule
     *
     * @param  AbstractCondition $conditions
     * @param  string            $formName
     * @param  string            $jsFormName
     * @return void
     */
    private function setConditionFormName(AbstractCondition $conditions, $formName, $jsFormName)
    {
        $conditions->setFormName($formName);
        $conditions->setJsFormObject($jsFormName);

        if ($conditions->getConditions() && is_array($conditions->getConditions())) {
            foreach ($conditions->getConditions() as $condition) {
                $this->setConditionFormName($condition, $formName, $jsFormName);
            }
        }
    }
}
