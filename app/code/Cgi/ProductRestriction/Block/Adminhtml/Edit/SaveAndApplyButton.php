<?php
/**
 * Copyright © 2020 CGI. All rights reserved.
 * See COPYING.txt for license details.
 *
 * @author    CGI <info.de@cgi.com>
 * @copyright 2020 CGI
 * @license   http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

namespace Cgi\ProductRestriction\Block\Adminhtml\Edit;

use Magento\Framework\View\Element\UiComponent\Control\ButtonProviderInterface;

/**
 * Class SaveAndApplyButton
 *
 * @package Cgi\ProductRestriction\Block\Adminhtml\Edit
 */
class SaveAndApplyButton extends GenericButton implements ButtonProviderInterface
{

    /**
     * Save Apply Action Button
     *
     * @return array|void
     */
    public function getButtonData()
    {
        $data = [];
        if ($this->canRender('save_apply')) {
            $data = [
                'label' => __('Save and Apply'),
                'class' => 'save',
                'on_click' => '',
                'sort_order' => 80,
                'data_attribute' => [
                    'mage-init' => [
                        'Magento_Ui/js/form/button-adapter' => [
                            'actions' => [
                                [
                                    'targetName' => 'productrestriction_rule_form.productrestriction_rule_form',
                                    'actionName' => 'save',
                                    'params' => [
                                        true,
                                        ['auto_apply' => 1],
                                    ]
                                ]
                            ]
                        ]
                    ],

                ]
            ];
        }
        return $data;
    }
}
