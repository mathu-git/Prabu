<?php

namespace Cgi\ProductRestriction\Controller\Adminhtml\Promo;

use Magento\CatalogRule\Model\Rule\Job;
use Magento\Framework\Controller\ResultFactory;

class ApplyRules extends Catalog
{
    public function execute()
    {

        $errorMessage = __('We can\'t apply the rules.');
        try {
            /** @var Job $ruleJob */
            $ruleJob = $this->_objectManager->get(\Magento\CatalogRule\Model\Rule\Job::class);
            $ruleJob->applyAll();

            if ($ruleJob->hasSuccess()) {
                $this->messageManager->addSuccessMessage($ruleJob->getSuccess());
                $this->_objectManager->create(\Magento\CatalogRule\Model\Flag::class)->loadSelf()->setState(0)->save();
            } elseif ($ruleJob->hasError()) {
                $this->messageManager->addErrorMessage($errorMessage . ' ' . $ruleJob->getError());
            }
        } catch (\Exception $e) {
            $this->_objectManager->create(\Psr\Log\LoggerInterface::class)->critical($e);
            $this->messageManager->addErrorMessage($errorMessage);
        }
        /** @var \Magento\Backend\Model\View\Result\Redirect $resultRedirect */
        $resultRedirect = $this->resultFactory->create(ResultFactory::TYPE_REDIRECT);
        return $resultRedirect->setPath('catalog_productrestriction/promo/catalog');
    }
}
