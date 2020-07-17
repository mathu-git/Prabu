<?php
/**
 * Copyright © 2020 CGI. All rights reserved.
 * See COPYING.txt for license details.
 *
 * @author    CGI <info.de@cgi.com>
 * @copyright 2020 CGI
 * @license   http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

declare(strict_types=1);

namespace Cgi\ProductRestriction\Block\Adminhtml\Promo;

use Magento\Backend\Block\Template\Context;

/**
 * Class Edit
 *
 * @package Cgi\ProductRestriction\Block\Adminhtml\Promo
 */
class Edit extends \Magento\Backend\Block\Template
{

    /**
     * Constructor
     *
     * @param Context $context
     * @param array   $data
     */
    public function __construct(
        Context $context,
        array $data = []
    ) {
        parent::__construct($context, $data);
    }
}
