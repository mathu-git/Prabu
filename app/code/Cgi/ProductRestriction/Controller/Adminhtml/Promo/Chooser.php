<?php
/**
 * Copyright © 2020 CGI. All rights reserved.
 * See COPYING.txt for license details.
 *
 * @author    CGI <info.de@cgi.com>
 * @copyright 2020 CGI
 * @license   http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */
namespace Cgi\ProductRestriction\Controller\Adminhtml\Promo;

use Cgi\ProductRestriction\Block\Adminhtml\Promo\Widget\Chooser\Sku;
use Magento\Framework\App\ResponseInterface;

/**
 * Class Chooser
 *
 * @package Cgi\ProductRestriction\Controller\Adminhtml\Promo
 */
class Chooser extends RestrictionAction
{

    /**
     * Get the attribute and check the condition
     *
     * @return ResponseInterface|\Magento\Framework\Controller\ResultInterface|void
     */
    public function execute()
    {
        if ($this->getRequest()->getParam('attribute') == 'sku') {
            $type = Sku::class;
        }
    }
}
