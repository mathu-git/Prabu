<?php
/**
 * Copyright © 2020 CGI. All rights reserved.
 * See COPYING.txt for license details.
 *
 * @author    CGI <info.de@cgi.com>
 * @copyright 2020 CGI
 * @license   http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

namespace Cgi\ProductRestriction\Model\ResourceModel;

use Magento\Catalog\Model\Product;
use Magento\Framework\App\ObjectManager;
use Magento\Framework\EntityManager\EntityManager;
use Magento\Framework\Model\AbstractModel;
use Magento\Framework\Pricing\PriceCurrencyInterface;
use Magento\Rule\Model\ResourceModel\AbstractResource;

/**
 * Class Rule
 * @package Cgi\ProductRestriction\Model\ResourceModel
 */
class Rule extends AbstractResource
{
    /**
     *
     */
    const SECONDS_IN_DAY = 86400;

    /**
     * @var \Psr\Log\LoggerInterface
     */
    protected $_logger;

    /**
     * @var \Magento\Framework\Event\ManagerInterface|null
     */
    protected $_eventManager = null;

    /**
     * @var \Magento\Eav\Model\Config
     */
    protected $_eavConfig;

    /**
     * @var \Magento\Framework\Stdlib\DateTime\DateTime
     */
    protected $_coreDate;

    /**
     * @var Product\ConditionFactory
     */
    protected $_conditionFactory;

    /**
     * @var \Magento\Store\Model\StoreManagerInterface
     */
    protected $_storeManager;

    /**
     * @var \Magento\Framework\Stdlib\DateTime
     */
    protected $dateTime;

    /**
     * @var PriceCurrencyInterface
     */
    protected $priceCurrency;

    /**
     * @var
     */
    protected $entityManager;

    /**
     * @var
     */
    protected $_associatedEntitiesMap;

    /**
     * Rule constructor.
     * @param \Magento\Framework\Model\ResourceModel\Db\Context $context
     * @param \Psr\Log\LoggerInterface $logger
     * @param \Magento\Framework\Event\ManagerInterface $eventManager
     * @param \Magento\Eav\Model\Config $eavConfig
     * @param \Magento\Framework\Stdlib\DateTime\DateTime $coreDate
     * @param Product\ConditionFactory $conditionFactory
     * @param \Magento\Store\Model\StoreManagerInterface $storeManager
     * @param \Magento\Framework\Stdlib\DateTime $dateTime
     * @param PriceCurrencyInterface $priceCurrency
     * @param null $connectionName
     */
    public function __construct(
        \Magento\Framework\Model\ResourceModel\Db\Context $context,
        \Psr\Log\LoggerInterface $logger,
        \Magento\Framework\Event\ManagerInterface $eventManager,
        \Magento\Eav\Model\Config $eavConfig,
        \Magento\Framework\Stdlib\DateTime\DateTime $coreDate,
        \Magento\Catalog\Model\Product\ConditionFactory $conditionFactory,
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        \Magento\Framework\Stdlib\DateTime $dateTime,
        PriceCurrencyInterface $priceCurrency,
        $connectionName = null)

    {
        $this->_eventManager = $eventManager;
        $this->_logger = $logger;
        $this->_eavConfig = $eavConfig;
        $this->_coreDate = $coreDate;
        $this->_conditionFactory = $conditionFactory;
        $this->_storeManager = $storeManager;
        $this->dateTime = $dateTime;
        $this->priceCurrency = $priceCurrency;
        parent::__construct($context, $connectionName);
    }

    /**
     *
     */
    protected function _construct()
    {
        $this->_init('catalogrule', 'rule_id');
    }

    /**
     * @param AbstractModel $rule
     * @return Rule
     */
    protected function _afterDelete(AbstractModel $rule)
    {
        $connection = $this->getConnection();
        $connection->delete(
            $this->getTable('catalogrule_product'),
            ['rule_id=?' => $rule->getId()]
        );
        $connection->delete(
            $this->getTable('catalogrule_group_website'),
            ['rule_id=?' => $rule->getId()]
        );
        return parent::_afterDelete($rule);
    }

    /**
     * @param $date
     * @param $websiteId
     * @param $customerGroupId
     * @param $productId
     * @return array
     */
    public function getRulesFromProduct($date, $websiteId, $customerGroupId, $productId)
    {
        $connection = $this->getConnection();
        if (is_string($date)) {
            $date = strtotime($date);
        }
        $select = $connection->select()
            ->from($this->getTable('catalogrule_product'))
            ->where('website_id = ?', $websiteId)
            ->where('customer_group_id = ?', $customerGroupId)
            ->where('product_id = ?', $productId)
            ->where('from_time = 0 or from_time < ?', $date)
            ->where('to_time = 0 or to_time > ?', $date);

        return $connection->fetchAll($select);
    }

    /**
     * @param AbstractModel $object
     * @param mixed $value
     * @param null $field
     * @return $this|Rule
     */
    public function load(AbstractModel $object, $value, $field = null)
    {
        $this->getEntityManager()->load($object, $value);
        return $this;
    }

    /**
     * @param AbstractModel $object
     * @return $this|Rule
     * @throws \Exception
     */
    public function save(AbstractModel $object)
    {
        $this->getEntityManager()->save($object);
        return $this;
    }

    /**
     * @param AbstractModel $object
     * @return $this|Rule
     * @throws \Exception
     */
    public function delete(AbstractModel $object)
    {
        $this->getEntityManager()->delete($object);
        return $this;
    }

    /**
     * @return EntityManager
     * @deprecated 100.1.0
     */
    private function getEntityManager()
    {
        if (null === $this->entityManager) {
            $this->entityManager = ObjectManager::getInstance()
                ->get(EntityManager::class);
        }
        return $this->entityManager;
    }
}
