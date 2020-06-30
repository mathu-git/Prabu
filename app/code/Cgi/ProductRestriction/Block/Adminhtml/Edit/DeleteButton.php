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
 * Class DeleteButton
 *
 * @package Cgi\ProductRestriction\Block\Adminhtml\Edit
 */
class DeleteButton extends GenericButton implements ButtonProviderInterface
{

    /**
     * Create the delete button
     *
     * @return array
     */
    public function getButtonData()
    {
        $ruleId = $this->getRuleId();
        if ($ruleId && $this->canRender('delete')) {
            $data = [
                'label' => ('Delete Rule'),
                'class' => 'delete',
                'on_click' => 'deleteConfirm(\'' . __(
                    'Are you sure you want to do this?'
                ) . '\', \'' . $this->urlBuilder->getUrl('*/*/delete', ['id' => $ruleId]) . '\', {data: {}})',
                'sort_order' => 20,
            ];
            return $data;
        }
    }
}
