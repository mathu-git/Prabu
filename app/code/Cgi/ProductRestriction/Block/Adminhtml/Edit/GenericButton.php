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

use Cgi\ProductRestriction\Controller\Adminhtml\RegistryConstants;
use Magento\Backend\Block\Widget\Context;
use Magento\Framework\Registry;

/**
 * Class GenericButton
 *
 * @package Cgi\ProductRestriction\Block\Adminhtml\Edit
 */
class GenericButton
{
    /**
     * Build Url
     *
     * @var \Magento\Framework\UrlInterface
     */
    protected $urlBuilder;

    /**
     * Magento Register the Parameter
     *
     * @var Registry
     */
    protected $registry;

    /**
     * GenericButton constructor
     *
     * @param Context  $context
     * @param Registry $registry
     */
    public function __construct(
        Context $context,
        Registry $registry
    ) {
        $this->urlBuilder = $context->getUrlBuilder();
        $this->registry = $registry;
    }

    /**
     * Get the current rule id
     *
     * @return |null
     */
    public function getRuleId()
    {
        $catalogRule = $this->registry->registry(RegistryConstants::CURRENT_RULE_ID);
        return $catalogRule ? $catalogRule->getId() : null;
    }

    /**
     * Get current Url
     *
     * @param  string $route
     * @param  array  $params
     * @return string
     */
    public function getUrl($route = '', $params = [])
    {
        return $this->urlBuilder->getUrl($route, $params);
    }

    /**
     * Render the name
     *
     * @param  $name
     * @return mixed
     */
    public function canRender($name)
    {
        return $name;
    }
}
