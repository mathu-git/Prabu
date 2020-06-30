<?php
/**
 * Copyright © 2020 CGI. All rights reserved.
 * See COPYING.txt for license details.
 *
 * @author    CGI <info.de@cgi.com>
 * @copyright 2020 CGI
 * @license   http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

namespace Cgi\ProductRestriction\Controller\Adminhtml\Promo;

use Magento\Backend\App\Action\Context;
use Magento\Backend\Model\Session;
use Magento\CatalogRule\Api\CatalogRuleRepositoryInterface;
use Magento\CatalogRule\Model\Rule;
use Magento\Framework\App\Action\HttpGetActionInterface as HttpGetActionInterface;
use Magento\Framework\Registry;
use Magento\Framework\View\Result\PageFactory;

/**
 * Class Edit
 *
 * @package Cgi\ProductRestriction\Controller\Adminhtml\Promo
 */
class Edit extends RestrictionAction implements HttpGetActionInterface
{

    /**
     * Resultpage Factory
     *
     * @var PageFactory
     */
    protected $resultPageFactory;

    /**
     * Core Registry
     *
     * @var coreRegistry
     */
    protected $coreRegistry;

    /**
     * Constructor
     *
     * @param Context     $context
     * @param PageFactory $resultPageFactory
     */
    public function __construct(
        Context $context,
        PageFactory $resultPageFactory,
        Registry $coreRegistry
    ) {
        $this->resultPageFactory = $resultPageFactory;
        $this->coreRegistry = $coreRegistry;
        parent::__construct($context, $coreRegistry);
    }

    /**
     * Execute view action
     *
     * @return \Magento\Framework\Controller\ResultInterface
     */
    public function execute()
    {
        $id = $this->getRequest()->getParam('id');
        $ruleRepository = $this->_objectManager->get(
            CatalogRuleRepositoryInterface::class
        );
        if ($id) {
            $model = $ruleRepository->get($id);
        } else {
            /**
             * @var Rule $model
             */
            $model = $this->_objectManager->create(Rule::class);
        }
        $data = $this->_objectManager->get(Session::class)->getPageData(true);
        if (!empty($data)) {
            $model->addData($data);
        }
        $model->getConditions()->setFormName('productrestriction_rule_form');
        $model->getConditions()->setJsFormObject(
            $model->getConditionsFieldSetId($model->getConditions()->getFormName())
        );
        $this->coreRegistry->register('current_promo_productionrestriction_rule', $model);
        $this->_initAction();
        $this->_view->getPage()->getConfig()->getTitle()->prepend(__('Product Restriction Rule'));
        $this->_view->getPage()->getConfig()->getTitle()->prepend(
            $model->getRuleId() ? $model->getName() : __('New Product Restriction Rule')
        );
        $breadcrumb = $id ? __('Edit Rule') : __('New Rule');
        $this->_addBreadcrumb($breadcrumb, $breadcrumb);
        $this->_view->renderLayout();
    }
}
