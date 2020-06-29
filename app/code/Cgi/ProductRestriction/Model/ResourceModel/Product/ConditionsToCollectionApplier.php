<?php
/**
 * Copyright © 2020 CGI. All rights reserved.
 * See COPYING.txt for license details.
 *
 * @author    CGI <info.de@cgi.com>
 * @copyright 2020 CGI
 * @license   http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

namespace cgi\ProductRestriction\Model\ResourceModel\Product;

use Magento\Framework\Api\SearchCriteria\CollectionProcessor\AdvancedFilterProcessor;
use Magento\Framework\Exception\InputException;
use Magento\Catalog\Model\ResourceModel\Product\Collection as ProductCollection;
use cgi\ProductRestriction\Model\Rule\Condition\ConditionsToSearchCriteriaMapper;
use cgi\ProductRestriction\Model\Rule\Condition\Combine;

/**
 * Class ConditionsToCollectionApplier
 * @package cgi\ProductRestriction\Model\ResourceModel\Product
 */
class ConditionsToCollectionApplier
{

    /**
     * @param Combine $conditions
     * @param ProductCollection $productCollection
     * @return ProductCollection
     */
    public function applyConditionsToCollection(
        Combine $conditions,
        ProductCollection $productCollection
    ): ProductCollection
    {

    }
}


