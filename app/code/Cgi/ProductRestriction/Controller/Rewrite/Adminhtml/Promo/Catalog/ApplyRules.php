<?php
/**
 * Copyright © 2020 CGI. All rights reserved.
 * See COPYING.txt for license details.
 *
 * @author    CGI <info.de@cgi.com>
 * @copyright 2020 CGI
 * @license   http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */
namespace Cgi\ProductRestriction\Controller\Rewrite\Adminhtml\Promo\Catalog;

use Magento\CatalogRule\Controller\Adminhtml\Promo\Catalog;
use Magento\CatalogRule\Model\Flag;
use Magento\Framework\App\Action\HttpPostActionInterface as HttpPostActionInterface;
use Magento\CatalogRule\Model\Rule\Job;
use Magento\Framework\Controller\ResultFactory;
use Psr\Log\LoggerInterface;

/**
 * Class ApplyRules
 *
 * @package Cgi\ProductRestriction\Controller\Rewrite\Adminhtml\Promo\Catalog
 */
class ApplyRules extends Catalog implements HttpPostActionInterface
{

    /**
     * Apply the product restriction rules and apply the role in selected condition products
     *
     * @return \Magento\Backend\Model\View\Result\Redirect|\Magento\Framework\App\ResponseInterface|\Magento\Framework\Controller\ResultInterface
     */
    public function execute()
    {
        $errorMessage = __('We can\'t apply the rules.');
        try {
            /**
 * @var Job $ruleJob
*/
            $ruleJob = $this->_objectManager->get(Job::class);
            $ruleJob->applyAll();

            if ($ruleJob->hasSuccess()) {
                $this->messageManager->addSuccessMessage($ruleJob->getSuccess());
                $this->_objectManager->create(Flag::class)->loadSelf()->setState(0)->save();
            } elseif ($ruleJob->hasError()) {
                $this->messageManager->addErrorMessage($errorMessage . ' ' . $ruleJob->getError());
            }
        } catch (\Exception $e) {
            $this->_objectManager->create(LoggerInterface::class)->critical($e);
            $this->messageManager->addErrorMessage($errorMessage);
        }

        /**
         * @var \Magento\Backend\Model\View\Result\Redirect $resultRedirect
         */
        $resultRedirect = $this->resultFactory->create(ResultFactory::TYPE_REDIRECT);
        $data = $this->getRequest()->getPostValue();
        if ($data && $data['is_product_restriction'] === '1') {
            return $resultRedirect->setPath('catalog_productrestriction/promo/catalog');
        } else {
            return $resultRedirect->setPath('catalog_rule/*/');
        }
    }
}
