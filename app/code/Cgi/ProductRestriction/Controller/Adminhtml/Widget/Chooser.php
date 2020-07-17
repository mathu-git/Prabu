<?php
/**
 * Copyright © 2020 CGI. All rights reserved.
 * See COPYING.txt for license details.
 *
 * @author    CGI <info.de@cgi.com>
 * @copyright 2020 CGI
 * @license   http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

namespace Cgi\ProductRestriction\Controller\Adminhtml\Widget;

use Cgi\ProductRestriction\Block\Adminhtml\Promo\Widget\Chooser\Sku;
use Magento\Catalog\Block\Adminhtml\Category\Checkboxes\Tree;
use Cgi\ProductRestriction\Controller\Adminhtml\Widget;

/**
 * Class Chooser
 *
 * @package Cgi\ProductRestriction\Controller\Adminhtml\Widget
 */
class Chooser extends Widget
{
    /**
     * Prepare the block for product and category chooser
     *
     * @return void
     */
    public function execute()
    {
        $request = $this->getRequest();
        switch ($request->getParam('attribute')) {
        case 'sku':
            $block = $this->_view->getLayout()->createBlock(
                Sku::class,
                'promo_widget_chooser_sku',
                ['data' => ['js_form_object' => $request->getParam('form')]]
            );
            break;

        case 'category_ids':
            $ids = $request->getParam('selected', []);
            if (is_array($ids)) {
                foreach ($ids as $key => &$id) {
                    $id = (int)$id;
                    if ($id <= 0) {
                        unset($ids[$key]);
                    }
                }

                $ids = array_unique($ids);
            } else {
                $ids = [];
            }

            $block = $this->_view->getLayout()->createBlock(
                Tree::class,
                'promo_widget_chooser_category_ids',
                ['data' => ['js_form_object' => $request->getParam('form')]]
            )->setCategoryIds(
                $ids
            );
            break;

        default:
            $block = false;
            break;
        }

        if ($block) {
            $this->getResponse()->setBody($block->toHtml());
        }
    }
}
