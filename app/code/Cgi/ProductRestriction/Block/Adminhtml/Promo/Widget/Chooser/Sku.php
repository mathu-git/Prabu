<?php
/**
 * Copyright © 2020 CGI. All rights reserved.
 * See COPYING.txt for license details.
 *
 * @author    CGI <info.de@cgi.com>
 * @copyright 2020 CGI
 * @license   http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

namespace Cgi\ProductRestriction\Block\Adminhtml\Promo\Widget\Chooser;

use Magento\Backend\Block\Template\Context;
use Magento\Backend\Block\Widget\Grid\Column;
use Magento\Backend\Block\Widget\Grid\Extended;
use Magento\Backend\Helper\Data;
use Magento\Catalog\Model\Product\Type;
use Magento\Eav\Model\ResourceModel\Entity\Attribute\Set\CollectionFactory;

/**
 * Class Sku
 *
 * @package Cgi\ProductRestriction\Block\Adminhtml\Promo\Widget\Chooser
 */
class Sku extends Extended
{
    /**
     * Category Type
     *
     * @var Type
     */
    protected $_catalogType;

    /**
     * @var \Magento\Catalog\Model\ResourceModel\Product\CollectionFactory
     */
    protected $_cpCollection;

    /**
     * @var \Magento\Catalog\Model\ResourceModel\Product\Collection
     */
    protected $_cpCollectionInstance;

    /**
     * @var CollectionFactory
     */
    protected $_eavAttSetCollection;

    /**
     * Sku constructor.
     *
     * @param Context                                                        $context
     * @param Data                                                           $backendHelper
     * @param CollectionFactory                                              $eavAttSetCollection
     * @param \Magento\Catalog\Model\ResourceModel\Product\CollectionFactory $cpCollection
     * @param Type                                                           $catalogType
     * @param array                                                          $data
     */
    public function __construct(
        Context $context,
        Data $backendHelper,
        CollectionFactory $eavAttSetCollection,
        \Magento\Catalog\Model\ResourceModel\Product\CollectionFactory $cpCollection,
        Type $catalogType,
        array $data = []
    ) {
        $this->_catalogType = $catalogType;
        $this->_cpCollection = $cpCollection;
        $this->_eavAttSetCollection = $eavAttSetCollection;
        parent::__construct($context, $backendHelper, $data);
    }

    /**
     * @return void
     */
    protected function _construct()
    {
        parent::_construct();

        if ($this->getRequest()->getParam('current_grid_id')) {
            $this->setId($this->getRequest()->getParam('current_grid_id'));
        } else {
            $this->setId('skuChooserGrid_' . $this->getId());
        }

        $form = $this->getJsFormObject();
        $this->setRowClickCallback("{$form}.chooserGridRowClick.bind({$form})");
        $this->setCheckboxCheckCallback("{$form}.chooserGridCheckboxCheck.bind({$form})");
        $this->setRowInitCallback("{$form}.chooserGridRowInit.bind({$form})");
        $this->setDefaultSort('sku');
        $this->setUseAjax(true);
        if ($this->getRequest()->getParam('collapse')) {
            $this->setIsCollapsed(true);
        }
    }

    /**
     * Get the define the filter collection
     *
     * @param  Column $column
     * @return $this
     */
    protected function _addColumnFilterToCollection($column)
    {
        // Set custom filter for in product flag
        if ($column->getId() == 'in_products') {
            $selected = $this->_getSelectedProducts();
            if (empty($selected)) {
                $selected = '';
            }
            if ($column->getFilter()->getValue()) {
                $this->getCollection()->addFieldToFilter('sku', ['in' => $selected]);
            } else {
                $this->getCollection()->addFieldToFilter('sku', ['nin' => $selected]);
            }
        } else {
            parent::_addColumnFilterToCollection($column);
        }
        return $this;
    }

    /**
     * Prepare Catalog Product Collection for attribute SKU in Promo Conditions SKU chooser
     *
     * @return $this
     */
    protected function _prepareCollection()
    {
        $collection = $this->_getCpCollectionInstance()->setStoreId(
            0
        )->addAttributeToSelect(
            'name',
            'type_id',
            'attribute_set_id'
        );

        $this->setCollection($collection);

        return parent::_prepareCollection();
    }

    /**
     * Get catalog product resource collection instance
     *
     * @return \Magento\Catalog\Model\ResourceModel\Product\Collection
     */
    protected function _getCpCollectionInstance()
    {
        if (!$this->_cpCollectionInstance) {
            $this->_cpCollectionInstance = $this->_cpCollection->create();
        }
        return $this->_cpCollectionInstance;
    }

    /**
     * Define Chooser Grid Columns and filters
     *
     * @return $this
     */
    protected function _prepareColumns()
    {
        $this->addColumn(
            'in_products',
            [
                'header_css_class' => 'a-center',
                'type' => 'checkbox',
                'name' => 'in_products',
                'values' => $this->_getSelectedProducts(),
                'align' => 'center',
                'index' => 'sku',
                'use_index' => true
            ]
        );

        $this->addColumn(
            'entity_id',
            ['header' => __('ID'), 'sortable' => true, 'width' => '60px', 'index' => 'entity_id']
        );

        $this->addColumn(
            'type',
            [
                'header' => __('Type'),
                'width' => '60px',
                'index' => 'type_id',
                'type' => 'options',
                'options' => $this->_catalogType->getOptionArray()
            ]
        );

        $sets = $this->_eavAttSetCollection->create()->setEntityTypeFilter(
            $this->_getCpCollectionInstance()->getEntity()->getTypeId()
        )->load()->toOptionHash();

        $this->addColumn(
            'set_name',
            [
                'header' => __('Attribute Set'),
                'width' => '100px',
                'index' => 'attribute_set_id',
                'type' => 'options',
                'options' => $sets
            ]
        );

        $this->addColumn(
            'chooser_sku',
            ['header' => __('SKU'), 'name' => 'chooser_sku', 'width' => '80px', 'index' => 'sku']
        );
        $this->addColumn(
            'chooser_name',
            ['header' => __('Product'), 'name' => 'chooser_name', 'index' => 'name']
        );

        return parent::_prepareColumns();
    }

    /**
     * Get the grid condition choose url
     *
     * @return string
     */
    public function getGridUrl()
    {
        return $this->getUrl(
            '*/*/chooser',
            ['_current' => true, 'current_grid_id' => $this->getId(), 'collapse' => null]
        );
    }

    /**
     * Define the selected the products
     *
     * @return mixed
     */
    protected function _getSelectedProducts()
    {
        $products = $this->getRequest()->getPost('selected', []);
        return $products;
    }
}
