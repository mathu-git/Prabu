<?php
/**
 *
 *  Copyright © 2020 CGI. All rights reserved.
 *  See COPYING.txt for license details.
 *
 *  @author    CGI <info.de@cgi.com>
 *  @copyright 2020 CGI
 *  @license   http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 *
 */
namespace Cgi\ProductRestriction\Model\ResourceModel\Grid;

/**
 * Class Collection
 * @package Cgi\ProductRestriction\Model\ResourceModel\Grid
 */
class Collection extends \Cgi\ProductRestriction\Model\ResourceModel\Rule\Collection
{
    /**
     * @return $this
     */
    public function _initSelect()
    {
        parent::_initSelect();
        $this->addWebsitesToResult();
        return $this;
    }
}

