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

use Magento\Backend\App\Action\Context;
use Magento\CatalogRule\Api\CatalogRuleRepositoryInterface;
use Magento\CatalogRule\Controller\Adminhtml\Promo\Catalog;
use Magento\CatalogRule\Model\Rule;
use Magento\Framework\App\Action\HttpPostActionInterface as HttpPostActionInterface;
use Magento\Framework\App\Request\DataPersistorInterface;
use Magento\Framework\Registry;
use Magento\Framework\Stdlib\DateTime\Filter\Date;
use Magento\Framework\Stdlib\DateTime\TimezoneInterface;
use Magento\Framework\App\ObjectManager;

/**
 * Class Save
 *
 * @package Cgi\ProductRestriction\Controller\Rewrite\Adminhtml\Promo\Catalog
 */
class Save extends Catalog implements HttpPostActionInterface
{
    /**
     * @var DataPersistorInterface
     */
    protected $dataPersistor;

    /**
     * Core registry
     *
     * @var Registry
     */
    protected $coreRegistry = null;

    /**
     * Date filter instance
     *
     * @var \Magento\Framework\Stdlib\DateTime\Filter\Date
     */
    protected $dateFilter;

    /**
     * @var TimezoneInterface
     */
    protected $localeDate;

    /**
     * Save constructor.
     *
     * @param Context                     $context
     * @param Registry                    $coreRegistry
     * @param Date                        $dateFilter
     * @param TimezoneInterface|null      $localeDate
     * @param DataPersistorInterface|null $dataPersistor
     */
    public function __construct(
        Context $context,
        Registry $coreRegistry,
        Date $dateFilter,
        TimezoneInterface $localeDate = null,
        DataPersistorInterface $dataPersistor = null
    ) {
        parent::__construct($context, $coreRegistry, $dateFilter);
        $this->_coreRegistry = $coreRegistry;
        $this->_dateFilter = $dateFilter;
        $this->localeDate = $localeDate ?? ObjectManager::getInstance()->get(
            TimezoneInterface::class
        );
        $this->dataPersistor = $dataPersistor ?? ObjectManager::getInstance()->get(
            DataPersistorInterface::class
        );
    }

    /**
     * @return \Magento\Framework\App\ResponseInterface|\Magento\Framework\Controller\ResultInterface|void
     */
    public function execute()
    {
        $data = $this->getRequest()->getPostValue();
        if ($data && $data['is_product_restriction'] === '1') {
            $this->saveProductRestriction($data);
            return;
        }

        if ($data) {
            /**
 * @var CatalogRuleRepositoryInterface $ruleRepository
*/
            $ruleRepository = $this->_objectManager->get(
                CatalogRuleRepositoryInterface::class
            );
            /**
 * @var Rule $model
*/
            $model = $this->_objectManager->create(Rule::class);

            try {
                $this->_eventManager->dispatch(
                    'adminhtml_controller_catalogrule_prepare_save',
                    ['request' => $this->getRequest()]
                );
                $data = $this->getRequest()->getPostValue();
                if (!$this->getRequest()->getParam('from_date')) {
                    $data['from_date'] = $this->localeDate->formatDate();
                }
                $filterValues = ['from_date' => $this->_dateFilter];
                if ($this->getRequest()->getParam('to_date')) {
                    $filterValues['to_date'] = $this->_dateFilter;
                }
                $inputFilter = new \Zend_Filter_Input(
                    $filterValues,
                    [],
                    $data
                );
                $data = $inputFilter->getUnescaped();
                $id = $this->getRequest()->getParam('rule_id');
                if ($id) {
                    $model = $ruleRepository->get($id);
                }

                $validateResult = $model->validateData(new \Magento\Framework\DataObject($data));
                if ($validateResult !== true) {
                    foreach ($validateResult as $errorMessage) {
                        $this->messageManager->addErrorMessage($errorMessage);
                    }
                    $this->_getSession()->setPageData($data);
                    $this->dataPersistor->set('catalog_rule', $data);
                    $this->_redirect('catalog_rule/*/edit', ['id' => $model->getId()]);
                    return;
                }

                if (isset($data['rule'])) {
                    $data['conditions'] = $data['rule']['conditions'];
                    unset($data['rule']);
                }

                unset($data['conditions_serialized']);
                unset($data['actions_serialized']);
                $model->loadPost($data);

                $this->_objectManager->get(\Magento\Backend\Model\Session::class)->setPageData($data);
                $this->dataPersistor->set('catalog_rule', $data);

                $ruleRepository->save($model);

                $this->messageManager->addSuccessMessage(__('You saved the rule.'));
                $this->_objectManager->get(\Magento\Backend\Model\Session::class)->setPageData(false);
                $this->dataPersistor->clear('catalog_rule');

                if ($this->getRequest()->getParam('auto_apply')) {
                    $this->getRequest()->setParam('rule_id', $model->getId());
                    $this->_forward('applyRules');
                } else {
                    if ($model->isRuleBehaviorChanged()) {
                        $this->_objectManager
                            ->create(\Magento\CatalogRule\Model\Flag::class)
                            ->loadSelf()
                            ->setState(1)
                            ->save();
                    }
                    if ($this->getRequest()->getParam('back')) {
                        $this->_redirect('catalog_rule/*/edit', ['id' => $model->getId()]);
                        return;
                    }
                    $this->_redirect('catalog_rule/*/');
                }
                return;
            } catch (LocalizedException $e) {
                $this->messageManager->addErrorMessage($e->getMessage());
            } catch (\Exception $e) {
                $this->messageManager->addErrorMessage(
                    __('Something went wrong while saving the rule data. Please review the error log.')
                );
                $this->_objectManager->get(\Psr\Log\LoggerInterface::class)->critical($e);
                $this->_objectManager->get(\Magento\Backend\Model\Session::class)->setPageData($data);
                $this->dataPersistor->set('catalog_rule', $data);
                $this->_redirect('catalog_rule/*/edit', ['id' => $this->getRequest()->getParam('rule_id')]);
                return;
            }
        }

        $this->_redirect('catalog_rule/*/');
    }

    /**
     * Save the product Restriction
     *
     * @param $data
     */
    public function saveProductRestriction($data)
    {
        if ($data) {
            /**
 * @var CatalogRuleRepositoryInterface $ruleRepository
*/
            $ruleRepository = $this->_objectManager->get(
                CatalogRuleRepositoryInterface::class
            );
            /**
 * @var Rule $model
*/
            $model = $this->_objectManager->create(Rule::class);

            try {
                $this->_eventManager->dispatch(
                    'adminhtml_controller_catalogrule_prepare_save',
                    ['request' => $this->getRequest()]
                );
                $data = $this->getRequest()->getPostValue();
                if (!$this->getRequest()->getParam('from_date')) {
                    $data['from_date'] = $this->localeDate->formatDate();
                }

                $filterValues = ['from_date' => $this->_dateFilter];
                if ($this->getRequest()->getParam('to_date')) {
                    $filterValues['to_date'] = $this->_dateFilter;
                }
                $inputFilter = new \Zend_Filter_Input(
                    $filterValues,
                    [],
                    $data
                );

                $data = $inputFilter->getUnescaped();
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
                $model->loadPost($data);

                $this->_objectManager->get(\Magento\Backend\Model\Session::class)->setPageData($data);
                $this->dataPersistor->set('catalog_rule', $data);

                $ruleRepository->save($model);

                $this->messageManager->addSuccessMessage(__('You saved the rule.'));
                $this->_objectManager->get(\Magento\Backend\Model\Session::class)->setPageData(false);
                $this->dataPersistor->clear('catalog_rule');

                if ($this->getRequest()->getParam('auto_apply')) {
                    $this->getRequest()->setParam('rule_id', $model->getId());
                    $this->_forward('applyRules');
                } else {
                    if ($model->isRuleBehaviorChanged()) {
                        $this->_objectManager
                            ->create(\Magento\CatalogRule\Model\Flag::class)
                            ->loadSelf()
                            ->setState(1)
                            ->save();
                    }

                    if ($this->getRequest()->getParam('back')) {
                        $this->_redirect('catalog_productrestriction/promo/edit', ['id' => $model->getId()]);
                        return;
                    }
                    $this->_redirect('catalog_productrestriction/promo/catalog');
                }
                return;
            } catch (LocalizedException $e) {
                $this->messageManager->addErrorMessage($e->getMessage());
            } catch (\Exception $e) {
                $this->messageManager->addErrorMessage(
                    __('Something went wrong while saving the rule data. Please review the error log.')
                );
                $this->_objectManager->get(\Psr\Log\LoggerInterface::class)->critical($e);
                $this->_objectManager->get(\Magento\Backend\Model\Session::class)->setPageData($data);
                $this->dataPersistor->set('catalog_rule', $data);
                $this->_redirect('catalog_productrestriction/promo/edit', ['id' => $this->getRequest()->getParam('rule_id')]);
                return;
            }
        }
        $this->_redirect('catalog_productrestriction/promo/catalog');
    }
}
