<?php
/**
 * Copyright © 2020 CGI. All rights reserved.
 * See COPYING.txt for license details.
 *
 * @author    CGI <info.de@cgi.com>
 * @copyright 2020 CGI
 * @license   http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

namespace Cgi\ProductRestriction\Model;

/**
 * Class Flag
 * @package Cgi\ProductRestriction\Model
 */
class Flag extends \Magento\Framework\Flag
{
    /**
     * Flag code
     *
     * @var string
     */
    protected $_flagCode = 'restriction_rules_dirty';
}
