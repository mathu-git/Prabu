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

use Cgi\ProductRestriction\Api\RestrictionRuleRepositoryInterface;
use Cgi\ProductRestriction\Model\Flag;
use Magento\CatalogRule\Model\Rule;
use Magento\Backend\App\Action;
use Magento\Backend\Model\Session;
use Magento\Framework\App\ResponseInterface;
use Magento\Backend\App\Action\Context;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Registry;
use Magento\Framework\Stdlib\DateTime\Filter\Date;
use Magento\Framework\App\Request\DataPersistorInterface;
use Psr\Log\LoggerInterface;

/**
 * Class Save
 *
 * @package Cgi\ProductRestriction\Controller\Adminhtml\Promo
 */
class Save extends Action
{
    /**
     * @var DataPersistorInterface
     */
    protected $dataPersistor;

    /**
     * Save constructor.
     *
     * @param Context                $context
     * @param Registry               $coreRegistry
     * @param Date                   $dateFilter
     * @param DataPersistorInterface $dataPersistor
     */
    public function __construct(
        Context $context,
        Registry $coreRegistry,
        Date $dateFilter,
        DataPersistorInterface $dataPersistor
    ) {
        $this->dataPersistor = $dataPersistor;
        $this->_coreRegistry = $coreRegistry;
        $this->_dateFilter = $dateFilter;
        parent::__construct($context);
    }

    /**
     * @return ResponseInterface|\Magento\Framework\Controller\ResultInterface|void
     */
    public function execute()
    {
        if ($this->getRequest()->getPostValue()) {
            $ruleRepository = $this->_objectManager->get(
                RestrictionRuleRepositoryInterface::class
            );
            /**
 * @var \Magento\CatalogRule\Model\Rule $model
*/
            $model = $this->_objectManager->create(Rule::class);
            try {
                $this->_eventManager->dispatch(
                    'adminhtml_controller_catalog_productrestriction_prepare_save',
                    ['request' => $this->getRequest()]
                );
                $data = $this->getRequest()->getPostValue();
                $id = $this->getRequest()->getParam('rule_id');
                if ($id) {
                    $model = $ruleRepository->get($id);
                }
                if (isset($data['rule'])) {
                    $data['conditions'] = $data['rule']['conditions'];
                    unset($data['rule']);
                }
                unset($data['conditions_serialized']);
                unset($data['actions_serialized']);
                $data['is_product_restriction'] = 1;
                $data['discount_amount'] = 10;
                $data['from_date'] = '2020-06-18';
                $data['to_date'] = '';
                $data['simple_action'] = 'by_percent';
                $data['stop_rules_processing'] = 0;
                $model->loadPost($data);
                $this->_objectManager->get(Session::class)->setPageData($data);
                $this->dataPersistor->set('productrestriction_rule', $data);
                $ruleRepository->save($model);
                $this->messageManager->addSuccessMessage(__('You saved the rule.'));
                $this->_objectManager->get(Session::class)->setPageData(false);
                $this->dataPersistor->clear('productrestriction_rule');

                if ($this->getRequest()->getParam('auto_apply')) {
                    $this->getRequest()->setParam('rule_id', $model->getId());
                    $this->_forward('applyRules');
                } else {
                    if ($model->isRuleBehaviorChanged()) {
                        $this->_objectManager
                            ->create(Flag::class)
                            ->loadSelf()
                            ->setState(1)
                            ->save();
                    }
                    if ($this->getRequest()->getParam('back')) {
                        $this->_redirect('catalog_productrestriction/*/edit', ['id' => $model->getId()]);
                        return;
                    }
                    $this->_redirect('catalog_productrestriction/*/');
                }
                return;
            } catch (LocalizedException $e) {
                $this->messageManager->addErrorMessage($e->getMessage());
            } catch (\Exception $e) {
                $this->messageManager->addErrorMessage(
                    __('Something went wrong while saving the rule data. Please review the error log.')
                );
                $this->_objectManager->get(LoggerInterface::class)->critical($e);
                $this->_objectManager->get(Session::class)->setPageData($data);
                $this->dataPersistor->set('productrestriction_rule', $data);
                $this->_redirect('catalog_productrestriction/*/edit', ['id' => $this->getRequest()->getParam('rule_id')]);
                return;
            }
        }
    }
}
