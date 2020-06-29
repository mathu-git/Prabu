<?php
declare(strict_types=1);

namespace Cgi\ProductRestriction\Controller\Adminhtml\Promo;

use Magento\Framework\App\Action\HttpGetActionInterface;
use Cgi\ProductRestriction\Controller\Adminhtml\Promo\RestrictionAction;

class Catalog extends RestrictionAction implements HttpGetActionInterface
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
        $resultPage = $this->resultPageFactory->create();
        $resultPage->getConfig()->getTitle()->prepend((__('Catalog Product Restriction')));
        return $resultPage;
    }
}
