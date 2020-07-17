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

use Magento\Backend\App\Action;
use Magento\CatalogRule\Api\CatalogRuleRepositoryInterface;

/**
 * Class Delete
 *
 * @package Cgi\ProductRestriction\Controller\Adminhtml\Promo
 */
class Delete extends Action
{

    /**
     * Rule Delete Action
     *
     * @return \Magento\Framework\App\ResponseInterface|\Magento\Framework\Controller\ResultInterface|void
     */
    public function execute()
    {
        $id = $this->getRequest()->getParam('id');
        if ($id) {
            try {
                /**
 * @var CatalogRuleRepositoryInterface $ruleRepository 
*/
                $ruleRepository = $this->_objectManager->get(
                    CatalogRuleRepositoryInterface::class
                );
                $ruleRepository->deleteById($id);

                $this->_objectManager->create(\Magento\CatalogRule\Model\Flag::class)->loadSelf()->setState(1)->save();
                $this->messageManager->addSuccessMessage(__('You deleted the rule.'));
                $this->_redirect('catalog_productrestriction/promo/catalog');
                return;
            } catch (LocalizedException $e) {
                $this->messageManager->addErrorMessage($e->getMessage());
            } catch (\Exception $e) {
                $this->messageManager->addErrorMessage(
                    __('We can\'t delete this rule right now. Please review the log and try again.')
                );
                $this->_objectManager->get(\Psr\Log\LoggerInterface::class)->critical($e);
                $this->_redirect('catalog_productrestriction/promo/edit', ['id' => $this->getRequest()->getParam('id')]);
                return;
            }
        }
        $this->messageManager->addErrorMessage(__('We can\'t find a rule to delete.'));
        $this->_redirect('catalog_productrestriction/promo/catalog');
    }
}
