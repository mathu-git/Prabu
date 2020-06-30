<?php
/**
 * Copyright © 2020 CGI. All rights reserved.
 * See COPYING.txt for license details.
 *
 * @author    CGI <info.de@cgi.com>
 * @copyright 2020 CGI
 * @license   http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

 namespace Cgi\ProductRestriction\Controller\Adminhtml;

 use Magento\Backend\App\Action;

 /**
  * Class Widget
  *
  * @package Cgi\ProductRestriction\Controller\Adminhtml
  */
abstract class Widget extends Action
{
    /**
      * Authorization level of a basic admin session
      *
      * @see _isAllowed()
      */
    const ADMIN_RESOURCE = 'Cgi_ProductRestriction::promo_catalog';
}
