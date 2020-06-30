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

use Magento\Backend\App\Action;
use Magento\Backend\App\Action\Context;
use Magento\Framework\Registry;

/**
 * Class RestrictionAction
 *
 * @package Cgi\ProductRestriction\Controller\Adminhtml\Promo
 */
abstract class RestrictionAction extends Action
{
    /**
     * Authorization level of a basic admin session
     *
     * @see _isAllowed()
     */
    const ADMIN_RESOURCE = 'Cgi_ProductRestriction::promo_catalog';

    /**
     * Core registry
     *
     * @var Registry
     */
    protected $coreRegistry = null;

    /**
     * Dirty rules notice message
     *
     * @var string
     */
    protected $_dirtyRulesNoticeMessage;

    /**
     * Date filter instance
     *
     * @var \Magento\Framework\Stdlib\DateTime\Filter\Date
     */
    protected $dateFilter;

    /**
     * Constructor
     *
     * @param Context  $context
     * @param Registry $coreRegistry
     * @param Date     $dateFilter
     */
    public function __construct(Context $context, Registry $coreRegistry)
    {
        parent::__construct($context);
        $this->_coreRegistry = $coreRegistry;
    }

    /**
     * Init action
     *
     * @return $this
     */
    protected function _initAction()
    {
        $this->_view->loadLayout();
        $this->_setActiveMenu(
            'Cgi_ProductRestriction::promo_catalog'
        )->_addBreadcrumb(
            __('Promotions'),
            __('Promotions')
        );
        return $this;
    }

    /**
     * @param $dirtyRulesNoticeMessage
     */
    public function setDirtyRulesNoticeMessage($dirtyRulesNoticeMessage)
    {
        $this->_dirtyRulesNoticeMessage = $dirtyRulesNoticeMessage;
    }

    /**
     * @return \Magento\Framework\Phrase|string
     */
    public function getDirtyRulesNoticeMessage()
    {
        $defaultMessage = __(
            'We found updated rules that are not applied. Please click "Apply Rules" to update your catalog.'
        );
        return $this->_dirtyRulesNoticeMessage ? $this->_dirtyRulesNoticeMessage : $defaultMessage;
    }
}
