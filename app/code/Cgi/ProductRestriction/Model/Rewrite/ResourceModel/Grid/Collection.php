<?php
/**
 * Copyright © 2020 CGI. All rights reserved.
 * See COPYING.txt for license details.
 *
 * @author    CGI <info.de@cgi.com>
 * @copyright 2020 CGI
 * @license   http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

namespace Cgi\ProductRestriction\Model\Rewrite\ResourceModel\Grid;

/**
 * Class Collection
 *
 * @package Cgi\ProductRestriction\Model\Rewrite\ResourceModel\Grid
 */
class Collection extends \Magento\CatalogRule\Model\ResourceModel\Rule\Collection
{

    /**
     * Product Restriction field
     */
    protected const RESTRICTION_GRID_FILTER = '0';

    /**
     * Filter the Catalog price rule based on the is Product Restriction field
     *
     * @return $this;
     */
    protected function _initSelect()
    {
        parent::_initSelect();
        $this->addFieldToFilter('is_product_restriction', self::RESTRICTION_GRID_FILTER);
        $this->addWebsitesToResult();

        return $this;
    }
}
