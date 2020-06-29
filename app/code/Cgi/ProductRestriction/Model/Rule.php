<?php
/**
 * Copyright © 2020 CGI. All rights reserved.
 * See COPYING.txt for license details.
 *
 * @author    CGI <info.de@cgi.com>
 * @copyright 2020 CGI
 * @license   http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */
namespace cgi\ProductRestriction\Model;

use Magento\Catalog\Model\Product;
use Magento\Catalog\Model\ProductFactory;
use Magento\Catalog\Model\ResourceModel\Product\CollectionFactory;
use Cgi\ProductRestriction\Api\Data\RuleExtensionInterface;
use Cgi\ProductRestriction\Api\Data\RuleInterface;
use Cgi\ProductRestriction\Model\ResourceModel\Rule as RuleResourceModel;
use Magento\CatalogRule\Model\Rule\Action\CollectionFactory as RuleCollectionFactory;
use Magento\CatalogRule\Model\Rule\Condition\CombineFactory;
use Magento\Customer\Model\Session;
use Magento\Framework\Api\AttributeValueFactory;
use Magento\Framework\Api\ExtensionAttributesFactory;
use Magento\Framework\App\Cache\TypeListInterface;
use Magento\Framework\App\ObjectManager;
use Magento\Framework\Data\Collection\AbstractDb;
use Magento\Framework\Data\FormFactory;
use Magento\Framework\DataObject\IdentityInterface;
use Magento\Framework\Model\Context;
use Magento\Framework\DataObject;
use Magento\Framework\Model\ResourceModel\AbstractResource;
use Magento\Framework\Model\ResourceModel\Iterator;
use Magento\Framework\Registry;
use Magento\Framework\Serialize\Serializer\Json;
use Magento\Framework\Stdlib\DateTime;
use Magento\Framework\Stdlib\DateTime\TimezoneInterface;
use Magento\Rule\Model\AbstractModel;
use Magento\Store\Model\StoreManagerInterface;
use Cgi\ProductRestriction\Model\ResourceModel\Product\ConditionsToCollectionApplier;

/**
 * Class Rule
 * @package cgi\ProductRestriction\Model
 */
class Rule extends AbstractModel implements RuleInterface, IdentityInterface
{
    /**
     * Prefix of model events names
     *
     * @var string
     */
    protected $_eventPrefix = 'productrestction_rule';

    /**
     * Parameter name in event
     *
     * In observe method you can use $observer->getEvent()->getRule() in this case
     *
     * @var string
     */
    protected $_eventObject = 'rule';

    /**
     * Store matched product Ids
     *
     * @var array
     */
    protected $_productIds;

    /**
     * Limitation for products collection
     *
     * @var int|array|null
     */
    protected $_productsFilter = null;

    /**
     * Store current date at "Y-m-d H:i:s" format
     *
     * @var string
     */
    protected $_now;

    /**
     * @var \Magento\Framework\App\Cache\TypeListInterface
     */
    protected $_cacheTypesList;

    /**
     * @var array
     */
    protected $_relatedCacheTypes;
    /**
     * @var \Magento\Framework\Stdlib\DateTime
     */
    protected $dateTime;

    /**
     * @var \Magento\Framework\Model\ResourceModel\Iterator
     */
    protected $_resourceIterator;

    /**
     * @var \Magento\Customer\Model\Session
     */
    protected $_customerSession;

    /**
     * @var \Magento\CatalogRule\Model\Rule\Condition\CombineFactory
     */
    protected $_combineFactory;

    /**
     * @var \Magento\CatalogRule\Model\Rule\Action\CollectionFactory
     */
    protected $_actionCollectionFactory;

    /**
     * @var \Magento\Catalog\Model\ProductFactory
     */
    protected $_productFactory;

    /**
     * @var \Magento\Store\Model\StoreManagerInterface
     */
    protected $_storeManager;

    /**
     * @var \Magento\Catalog\Model\ResourceModel\Product\CollectionFactory
     */
    protected $_productCollectionFactory;

    /**
     * @var \Magento\CatalogRule\Model\Indexer\Rule\RuleProductProcessor;
     */
    protected $_ruleProductProcessor;

    /**
     * @var Data\Condition\Converter
     */
    protected $ruleConditionConverter;

    /**
     * @var ConditionsToCollectionApplier
     */
    private $conditionsToCollectionApplier;

    /**
     * @var array
     */
    private $websitesMap;

    /**
     * @var RuleResourceModel
     */
    private $ruleResourceModel;

    /**
     * Rule constructor.
     * @param Context $context
     * @param Registry $registry
     * @param FormFactory $formFactory
     * @param TimezoneInterface $localeDate
     * @param CollectionFactory $productCollectionFactory
     * @param StoreManagerInterface $storeManager
     * @param ProductFactory $productFactory
     * @param Iterator $resourceIterator
     * @param Session $customerSession
     * @param TypeListInterface $cacheTypesList
     * @param DateTime $dateTime
     * @param AbstractResource|null $resource
     * @param AbstractDb|null $resourceCollection
     * @param array $relatedCacheTypes
     * @param array $data
     * @param ExtensionAttributesFactory|null $extensionFactory
     * @param AttributeValueFactory|null $customAttributeFactory
     * @param Json|null $serializer
     * @param RuleResourceModel|null $ruleResourceModel
     * @param ConditionsToCollectionApplier|null $conditionsToCollectionApplier
     * @param RuleCollectionFactory $actionCollectionFactory
     * @param CombineFactory $combineFactory
     */
    public function __construct(
        Context $context,
        Registry $registry,
        FormFactory $formFactory,
        TimezoneInterface $localeDate,
        CollectionFactory $productCollectionFactory,
        StoreManagerInterface $storeManager,
        ProductFactory $productFactory,
        Iterator $resourceIterator,
        Session $customerSession,
        TypeListInterface $cacheTypesList,
        DateTime $dateTime,
        AbstractResource $resource = null,
        AbstractDb $resourceCollection = null,
        array $relatedCacheTypes = [],
        array $data = [],
        ExtensionAttributesFactory $extensionFactory = null,
        AttributeValueFactory $customAttributeFactory = null,
        Json $serializer = null,
        RuleResourceModel $ruleResourceModel = null,
        ConditionsToCollectionApplier $conditionsToCollectionApplier = null,
        RuleCollectionFactory $actionCollectionFactory,
        CombineFactory $combineFactory
    )
    {
        $this->_productCollectionFactory = $productCollectionFactory;
        $this->_storeManager = $storeManager;
        $this->_productFactory = $productFactory;
        $this->_resourceIterator = $resourceIterator;
        $this->_customerSession = $customerSession;
        $this->_cacheTypesList = $cacheTypesList;
        $this->_relatedCacheTypes = $relatedCacheTypes;
        $this->dateTime = $dateTime;
        $this->ruleResourceModel = $ruleResourceModel ?: ObjectManager::getInstance()->get(RuleResourceModel::class);
        $this->_combineFactory = $combineFactory;
        $this->_actionCollectionFactory = $actionCollectionFactory;
        $this->conditionsToCollectionApplier = $conditionsToCollectionApplier
            ?? ObjectManager::getInstance()->get(ConditionsToCollectionApplier::class);
        parent::__construct(
            $context,
            $registry,
            $formFactory,
            $localeDate,
            $resource,
            $resourceCollection,
            $data,
            $extensionFactory,
            $customAttributeFactory,
            $serializer
        );
    }

    /**
     * Init resource model and id field
     *
     * @return void
     */
    protected function _construct()
    {
        parent::_construct();
        $this->_init(RuleResourceModel::class);
        $this->setIdFieldName('rule_id');
    }

    /**
     * @return \Magento\CatalogRule\Model\Rule\Condition\Combine|\Magento\Rule\Model\Condition\Combine
     */
    public function getConditionsInstance()
    {
        return $this->_combineFactory->create();
    }

    /**
     * @return \Magento\CatalogRule\Model\Rule\Action\Collection|\Magento\Rule\Model\Action\Collection
     */
    public function getActionsInstance()
    {
        return $this->_actionCollectionFactory->create();
    }

    /**
     * @return int|mixed|null
     */
    public function getRuleId()
    {
        return $this->getData(self::RULE_ID);
    }

    /**
     * @param int $ruleId
     * @return Rule
     */
    public function setRuleId($ruleId)
    {
        return $this->setData(self::RULE_ID, $ruleId);
    }

    /**
     * @return mixed|string|null
     */
    public function getName()
    {
        return $this->getData(self::NAME);
    }

    /**
     * @param string $name
     * @return Rule
     */
    public function setName($name)
    {
        return $this->setData(self::NAME, $name);
    }

    /**
     * @return mixed|string|null
     */
    public function getDescription()
    {
        return $this->getData(self::DESCRIPTION);
    }

    /**
     * @param string $description
     * @return Rule
     */
    public function setDescription($description)
    {
        return $this->setData(self::DESCRIPTION, $description);
    }

    /**
     * @return int|mixed|null
     */
    public function getIsActive()
    {
        return $this->getData(self::IS_ACTIVE);
    }

    /**
     * @param int $isActive
     * @return Rule
     */
    public function setIsActive($isActive)
    {
        return $this->setData(self::IS_ACTIVE, $isActive);
    }

    /**
     * @return \Cgi\ProductRestriction\Api\Data\ConditionInterface|null
     */
    public function getRuleCondition()
    {
        return $this->getRuleConditionConverter()->arrayToDataModel($this->getConditions()->asArray());
    }

    /**
     * @param \Cgi\ProductRestriction\Api\Data\ConditionInterface $condition
     * @return $this|Rule
     */
    public function setRuleCondition($condition)
    {
        $this->getConditions()
            ->setConditions([])
            ->loadArray($this->getRuleConditionConverter()->dataModelToArray($condition));
        return $this;
    }

    /**
     * @return int|mixed|null
     */
    public function getStopRulesProcessing()
    {
        return $this->getData(self::STOP_RULES_PROCESSING);
    }

    /**
     * @param int $isStopProcessing
     * @return Rule
     */
    public function setStopRulesProcessing($isStopProcessing)
    {
        return $this->setData(self::STOP_RULES_PROCESSING, $isStopProcessing);
    }

    /**
     * @return int|mixed|null
     */
    public function getSortOrder()
    {
        return $this->getData(self::SORT_ORDER);
    }

    /**
     * @param int $sortOrder
     * @return Rule
     */
    public function setSortOrder($sortOrder)
    {
        return $this->setData(self::SORT_ORDER, $sortOrder);
    }

    /**
     * @return mixed|string|null
     */
    public function getSimpleAction()
    {
        return $this->getData(self::SIMPLE_ACTION);
    }

    /**
     * @param string $action
     * @return Rule
     */
    public function setSimpleAction($action)
    {
        return $this->setData(self::SIMPLE_ACTION, $action);
    }

    /**
     * @inheritdoc
     */
    public function getDiscountAmount()
    {
        return $this->getData(self::DISCOUNT_AMOUNT);
    }

    /**
     * @inheritdoc
     */
    public function setDiscountAmount($amount)
    {
        return $this->setData(self::DISCOUNT_AMOUNT, $amount);
    }

    /**
     * @return string
     */
    public function getFromDate()
    {
        return $this->getData('from_date');
    }

    /**
     * @return string
     */
    public function getToDate()
    {
        return $this->getData('to_date');
    }

    /**
     * @return \Magento\Framework\Api\ExtensionAttributesInterface|null
     */
    public function getExtensionAttributes()
    {
        return $this->_getExtensionAttributes();
    }

    /**
     * @param RuleExtensionInterface $extensionAttributes
     * @return Rule
     */
    public function setExtensionAttributes(RuleExtensionInterface $extensionAttributes)
    {
        return $this->_setExtensionAttributes($extensionAttributes);
    }

    /**
     * @return string[]
     */
    public function getIdentities()
    {
        return [''];
    }

    /**
     * @param string $formName
     * @return string
     */
    public function getConditionsFieldSetId($formName = '')
    {
        return $formName . 'rule_conditions_fieldset_' . $this->getId();
    }

    /**
     * @return Data\Condition\Converter|mixed
     */
    private function getRuleConditionConverter()
    {
        if (null === $this->ruleConditionConverter) {
            $this->ruleConditionConverter = ObjectManager::getInstance()
                ->get(Converter::class);
        }
        return $this->ruleConditionConverter;
    }

    /**
     * @return mixed|null
     */
    public function getCustomerGroupIds()
    {
        if (!$this->hasCustomerGroupIds()) {
            $customerGroupIds = $this->ruleResourceModel->getCustomerGroupIds($this->getId());
            $this->setData('customer_group_ids', (array)$customerGroupIds);
        }
        return $this->_getData('customer_group_ids');
    }

    /**
     * @return string
     */
    public function getNow()
    {
        if (!$this->_now) {
            return (new \DateTime())->format(DateTime::DATETIME_PHP_FORMAT);
        }
        return $this->_now;
    }

    /**
     * @param $now
     */
    public function setNow($now)
    {
        $this->_now = $now;
    }

    /**
     * @return array|null
     */
    public function getMatchingProductIds()
    {
        if ($this->_productIds === null) {
            $this->_productIds = [];
            $this->setCollectedAttributes([]);

            if ($this->getWebsiteIds()) {
                /** @var $productCollection \Magento\Catalog\Model\ResourceModel\Product\Collection */
                $productCollection = $this->_productCollectionFactory->create();
                $productCollection->addWebsiteFilter($this->getWebsiteIds());
                if ($this->_productsFilter) {
                    $productCollection->addIdFilter($this->_productsFilter);
                }
                $this->getConditions()->collectValidatedAttributes($productCollection);

                if ($this->canPreMapProducts()) {
                    $productCollection = $this->conditionsToCollectionApplier
                        ->applyConditionsToCollection($this->getConditions(), $productCollection);
                }

                $this->_resourceIterator->walk(
                    $productCollection->getSelect(),
                    [[$this, 'callbackValidateProduct']],
                    [
                        'attributes' => $this->getCollectedAttributes(),
                        'product' => $this->_productFactory->create()
                    ]
                );
            }
        }

        return $this->_productIds;
    }

    /**
     * @return bool
     */
    private function canPreMapProducts()
    {
        $conditions = $this->getConditions();

        // No need to map products if there is no conditions in rule
        if (!$conditions || !$conditions->getConditions()) {
            return false;
        }

        return true;
    }

    /**
     * @param $args
     */
    public function callbackValidateProduct($args)
    {
        $product = clone $args['product'];
        $product->setData($args['row']);

        $websites = $this->_getWebsitesMap();
        $results = [];

        foreach ($websites as $websiteId => $defaultStoreId) {
            $product->setStoreId($defaultStoreId);
            $results[$websiteId] = $this->getConditions()->validate($product);
        }
        $this->_productIds[$product->getId()] = $results;
    }

    /**
     * @return array|null
     */
    protected function _getWebsitesMap()
    {
        if ($this->websitesMap === null) {
            $this->websitesMap = [];
            $websites = $this->_storeManager->getWebsites();
            foreach ($websites as $website) {
                // Continue if website has no store to be able to create catalog rule for website without store
                if ($website->getDefaultStore() === null) {
                    continue;
                }
                $this->websitesMap[$website->getId()] = $website->getDefaultStore()->getId();
            }
        }

        return $this->websitesMap;
    }

    /**
     * @param $dateTs
     * @param $websiteId
     * @param $customerGroupId
     * @param $productId
     * @return array
     */
    protected function _getRulesFromProduct($dateTs, $websiteId, $customerGroupId, $productId)
    {
        return $this->ruleResourceModel->getRulesFromProduct($dateTs, $websiteId, $customerGroupId, $productId);
    }

    /**
     * @param $productIds
     */
    public function setProductsFilter($productIds)
    {
        $this->_productsFilter = $productIds;
    }

    /**
     * @return array|int|null
     */
    public function getProductsFilter()
    {
        return $this->_productsFilter;
    }

    /**
     * @return $this
     */
    protected function _invalidateCache()
    {
        if (count($this->_relatedCacheTypes)) {
            $this->_cacheTypesList->invalidate($this->_relatedCacheTypes);
        }
        return $this;
    }

    /**
     * @return Rule
     */
    public function afterDelete()
    {
        $this->_ruleProductProcessor->getIndexer()->invalidate();
        return parent::afterDelete();
    }

    /**
     * @return bool
     */
    public function isRuleBehaviorChanged()
    {
        if (!$this->isObjectNew()) {
            $arrayDiff = $this->dataDiff($this->getOrigData(), $this->getStoredData());
            unset($arrayDiff['name']);
            unset($arrayDiff['description']);
            if (empty($arrayDiff)) {
                return false;
            }
        }
        return true;
    }

    /**
     * @param $array1
     * @param $array2
     * @return array
     */
    protected function dataDiff($array1, $array2)
    {
        $result = [];
        foreach ($array1 as $key => $value) {
            if (array_key_exists($key, $array2)) {
                if ($value != $array2[$key]) {
                    $result[$key] = true;
                }
            } else {
                $result[$key] = true;
            }
        }
        return $result;
    }

    /**
     * @param string $attributeCode
     * @return \Magento\Framework\Api\AttributeInterface|mixed|null
     */
    public function getCustomAttribute($attributeCode)
    {
        return $this->getCustomAttribute();
    }

    /**
     * @param string $attributeCode
     * @param mixed $attributeValue
     * @return Rule|\Magento\Framework\Api\CustomAttributesDataInterface|\Magento\Framework\Model\AbstractExtensibleModel|mixed
     */
    public function setCustomAttribute($attributeCode, $attributeValue)
    {
        return $this->setCustomAttribute($attributeCode, $attributeValue);
    }

    /**
     * @return \Magento\Framework\Api\AttributeInterface[]|mixed|null
     */
    public function getCustomAttributes()
    {
        return $this->getCustomAttributes();
    }

    /**
     * @param array $attributes
     * @return Rule|\Magento\Framework\Api\CustomAttributesDataInterface|\Magento\Framework\Model\AbstractExtensibleModel|mixed
     */
    public function setCustomAttributes(array $attributes)
    {
        return $this->setCustomAttributes($attributes);
    }
}
