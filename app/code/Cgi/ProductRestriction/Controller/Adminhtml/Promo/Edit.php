<?php
declare(strict_types=1);

namespace Cgi\ProductRestriction\Controller\Adminhtml\Promo;

use Magento\CatalogRule\Model\Rule;
use Magento\Framework\App\Action\HttpGetActionInterface as HttpGetActionInterface;

class Edit extends RestrictionAction implements HttpGetActionInterface
{

    protected $resultPageFactory;

    /**
     * Constructor
     *
     * @param \Magento\Backend\App\Action\Context  $context
     * @param \Magento\Framework\View\Result\PageFactory $resultPageFactory
     */
    public function __construct(
        \Magento\Backend\App\Action\Context $context,
        \Magento\Framework\View\Result\PageFactory $resultPageFactory,
        \Magento\Framework\Registry $coreRegistry
    ) {
        $this->resultPageFactory = $resultPageFactory;
        $this->_coreRegistry = $coreRegistry;
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
            \Magento\CatalogRule\Api\CatalogRuleRepositoryInterface::class
        );
        if ($id) {
            try {
                $model = $ruleRepository->get($id);
            } catch (\Magento\Framework\Exception\NoSuchEntityException $exception) {
                $this->messageManager->addErrorMessage(__('This rule no longer exists.'));
                $this->_redirect('catalog_productrestriction/*');
                return;
            }
        } else {
            /** @var Rule $model */
            $model = $this->_objectManager->create(Rule::class);
        }
        $data = $this->_objectManager->get(\Magento\Backend\Model\Session::class)->getPageData(true);
        if (!empty($data)) {
            $model->addData($data);
        }
        $model->getConditions()->setFormName('productrestriction_rule_form');
        $model->getConditions()->setJsFormObject(
            $model->getConditionsFieldSetId($model->getConditions()->getFormName())
        );
        $this->_coreRegistry->register('current_promo_productionrestriction_rule', $model);
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

