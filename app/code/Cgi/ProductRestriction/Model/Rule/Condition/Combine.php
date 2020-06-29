<?php
/**
 * Copyright © 2020 CGI. All rights reserved.
 * See COPYING.txt for license details.
 *
 * @author    CGI <info.de@cgi.com>
 * @copyright 2020 CGI
 * @license   http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

namespace Cgi\ProductRestriction\Model\Rule\Condition;

use Magento\Rule\Model\Condition\Context;

/**
 * Class Combine
 * @package Cgi\ProductRestriction\Model\Rule\Condition
 */
class Combine extends \Magento\Rule\Model\Condition\Combine
{
    /**
     * @var ProductFactory
     */
    protected $_productFactory;

    /**
     * Combine constructor.
     * @param Context $context
     * @param ProductFactory $conditionFactory
     * @param array $data
     */
    public function __construct(
        Context $context,
        ProductFactory $conditionFactory,
        array $data = []
    ) {
        $this->_productFactory = $conditionFactory;
        parent::__construct($context, $data);
        $this->setType(Combine::class);
    }
    /**
     * @return array
     */
    public function getNewChildSelectOptions()
    {
        $productAttributes = $this->_productFactory->create()->loadAttributeOptions()->getAttributeOption();
        $attributes = [];
        foreach ($productAttributes as $code => $label) {
            $attributes[] = [
                'value' => 'Cgi\ProductRestriction\Model\Rule\Condition\Product|' . $code,
                'label' => $label,
            ];

            $conditions = parent::getNewChildSelectOptions();
            $conditions = array_merge_recursive(
                $conditions,
                [
                    [
                        'value' => \Magento\CatalogRule\Model\Rule\Condition\Combine::class,
                        'label' => __('Conditions Combination'),
                    ],
                    ['label' => __('Product Attribute'), 'value' => $attributes]
                ]
            );
            return $conditions;
        }
    }

    /**
     * @param array $productCollection
     * @return $this
     */
    public function collectValidatedAttributes($productCollection)
    {
        foreach ($this->getConditions() as $condition) {
            /** @var Product|Combine $condition */
            $condition->collectValidatedAttributes($productCollection);
        }
        return $this;
    }
}

