<?php
/**
 * Copyright © 2020 CGI. All rights reserved.
 * See COPYING.txt for license details.
 *
 * @author    CGI <info.de@cgi.com>
 * @copyright 2020 CGI
 * @license   http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

namespace Cgi\ProductRestriction\Model\Rewrite\Resource\Rule\Catalog;

use Magento\CatalogRule\Model\ResourceModel\Rule\AssociatedEntityMap;
use Magento\CatalogRule\Model\Rule;
use Magento\Framework\Data\Collection\Db\FetchStrategyInterface;
use Magento\Framework\Data\Collection\EntityFactoryInterface;
use Magento\Framework\DB\Adapter\AdapterInterface;
use Magento\Framework\Event\ManagerInterface;
use Magento\Framework\Model\ResourceModel\Db\AbstractDb;
use Magento\Framework\Serialize\Serializer\Json;
use Magento\Framework\App\ObjectManager;
use Magento\Rule\Model\ResourceModel\Rule\Collection\AbstractCollection;
use Psr\Log\LoggerInterface;

/**
 * Class Collection
 *
 * @package Cgi\ProductRestriction\Model\Rewrite\Resource\Rule\Catalog
 */
class Collection extends AbstractCollection
{
    /**
     * Store associated with rule entities information map
     *
     * @var array
     */
    protected $_associatedEntitiesMap;

    /**
     * @var Json
     */
    protected $serializer;

    /**
     * Collection constructor.
     *
     * @param EntityFactoryInterface $entityFactory
     * @param LoggerInterface        $logger
     * @param FetchStrategyInterface $fetchStrategy
     * @param ManagerInterface       $eventManager
     * @param AdapterInterface       $connection
     * @param AbstractDb             $resource
     * @param Json|null              $serializer
     */
    public function __construct(
        EntityFactoryInterface $entityFactory,
        LoggerInterface $logger,
        FetchStrategyInterface $fetchStrategy,
        ManagerInterface $eventManager,
        AdapterInterface $connection = null,
        AbstractDb $resource = null,
        Json $serializer = null
    ) {
        parent::__construct($entityFactory, $logger, $fetchStrategy, $eventManager, $connection, $resource);
        $this->_associatedEntitiesMap = $this->getAssociatedEntitiesMap();
        $this->serializer = $serializer ?: ObjectManager::getInstance()->get(Json::class);
    }

    /**
     * Set resource model
     *
     * @return             void
     * @codeCoverageIgnore
     */
    protected function _construct()
    {
        $this->_init(Rule::class, \Magento\CatalogRule\Model\ResourceModel\Rule::class);
    }

    /**
     * Find product attribute in conditions or actions
     *
     * @param  string $attributeCode
     * @return Collection
     * @api
     */
    public function addAttributeInConditionFilter($attributeCode)
    {
        $match = sprintf('%%%s%%', substr($this->serializer->serialize(['attribute' => $attributeCode]), 1, -1));
        $this->addFieldToFilter('conditions_serialized', ['like' => $match]);

        return $this;
    }

    /**
     * @param  string $entityType
     * @param  string $objectField
     * @throws \Magento\Framework\Exception\LocalizedException
     * @return void
     */
    protected function mapAssociatedEntities($entityType, $objectField)
    {
        if (!$this->_items) {
            return;
        }

        $entityInfo = $this->_getAssociatedEntityInfo($entityType);
        $ruleIdField = $entityInfo['rule_id_field'];
        $entityIds = $this->getColumnValues($ruleIdField);

        $select = $this->getConnection()->select()->from(
            $this->getTable($entityInfo['associations_table'])
        )->where(
            $ruleIdField . ' IN (?)',
            $entityIds
        );

        $associatedEntities = $this->getConnection()->fetchAll($select);

        array_map(
            function ($associatedEntity) use ($entityInfo, $ruleIdField, $objectField) {
                $item = $this->getItemByColumnValue($ruleIdField, $associatedEntity[$ruleIdField]);
                $itemAssociatedValue = $item->getData($objectField) === null ? [] : $item->getData($objectField);
                $itemAssociatedValue[] = $associatedEntity[$entityInfo['entity_id_field']];
                $item->setData($objectField, $itemAssociatedValue);
            }, $associatedEntities
        );
    }

    /**
     * @return $this
     */
    protected function _afterLoad()
    {
        $this->mapAssociatedEntities('website', 'website_ids');
        $this->mapAssociatedEntities('customer_group', 'customer_group_ids');

        $this->setFlag('add_websites_to_result', false);
        return parent::_afterLoad();
    }

    /**
     * Limit rules collection by specific customer group
     *
     * @param  int $customerGroupId
     * @return $this
     */
    public function addCustomerGroupFilter($customerGroupId)
    {
        $entityInfo = $this->_getAssociatedEntityInfo('customer_group');
        if (!$this->getFlag('is_customer_group_joined')) {
            $this->setFlag('is_customer_group_joined', true);
            $this->getSelect()->join(
                ['customer_group' => $this->getTable($entityInfo['associations_table'])],
                $this->getConnection()
                    ->quoteInto('customer_group.' . $entityInfo['entity_id_field'] . ' = ?', $customerGroupId)
                . ' AND main_table.' . $entityInfo['rule_id_field'] . ' = customer_group.'
                . $entityInfo['rule_id_field'],
                []
            );
        }
        return $this;
    }

    /**
     * Associate the entity mapping process
     *
     * @return     array
     * @deprecated 100.1.0
     */
    public function getAssociatedEntitiesMap()
    {
        if (!$this->_associatedEntitiesMap) {
            $this->_associatedEntitiesMap = ObjectManager::getInstance()
                ->get(AssociatedEntityMap::class)
                ->getData();
        }
        return $this->_associatedEntitiesMap;
    }
}
