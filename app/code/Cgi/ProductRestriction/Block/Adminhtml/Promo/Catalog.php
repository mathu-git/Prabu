<?php
/**
 * Copyright © 2020 CGI. All rights reserved.
 * See COPYING.txt for license details.
 *
 * @author    CGI <info.de@cgi.com>
 * @copyright 2020 CGI
 * @license   http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */
namespace Cgi\ProductRestriction\Block\Adminhtml\Promo;

/**
 * Class Catalog
 *
 * @package Cgi\ProductRestriction\Block\Adminhtml\Promo
 */
class Catalog extends \Magento\Backend\Block\Widget\Grid\Container
{

    /**
     * @return void
     */
    protected function _construct()
    {
        $this->_blockGroup = 'Cgi_ProductRestriction';
        $this->_controller = 'adminhtml_promo_catalog';
        $this->_headerText = __('Product Restriction');
        $this->_addButtonLabel = __('Add New Rule');
        parent::_construct();

        //Create the Apply Button
        $this->buttonList->add(
            'apply_rules',
            [
                'label' => __('Apply Rules'),
                'onclick' => "location.href='" . $this->getUrl('catalog_productrestriction/promo/applyRules') . "'",
                'class' => 'apply'
            ]
        );
    }
}
