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
namespace Cgi\ProductRestriction\Controller\Adminhtml\Promo;

use Magento\Backend\App\Action;
use Magento\Backend\App\Action\Context;
use Cgi\ProductRestriction\Controller\Adminhtml\Promo\RestrictionAction;
use Magento\Framework\App\Action\HttpGetActionInterface as HttpGetActionInterface;
use Magento\Framework\View\Result\PageFactory;

class NewAction extends RestrictionAction implements HttpGetActionInterface
{
    protected $resultPageFactory;

    public function __construct(
        Context $context,
        PageFactory $resultPageFactory,
        \Magento\Framework\Registry $coreRegistry
    ) {
        $this->resultPageFactory = $resultPageFactory;
        $this->_coreRegistry = $coreRegistry;
        parent::__construct($context, $coreRegistry);
    }
    /**
     * @return void
     */
    public function execute()
    {
        $this->_forward('edit');
    }
}
