<?php
/**
 * Copyright © 2020 CGI. All rights reserved.
 * See COPYING.txt for license details.
 *
 * @author    CGI <info.de@cgi.com>
 * @copyright 2020 CGI
 * @license   http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

namespace Cgi\ProductRestriction\Observer;

use Magento\Customer\Model\Session;
use Magento\Framework\App\ResponseFactory;
use Magento\Framework\Event\Observer;
use Magento\Framework\Event\ObserverInterface;
use Cgi\ProductRestriction\Helper\Data;
use Magento\Framework\ObjectManagerInterface;
use Magento\Framework\UrlInterface;

class CatalogProductView implements ObserverInterface
{
    /**
     * @var ResponseFactory
     */
    protected $responseFactory;

    /**
     * @var Session
     */
    protected $customerSession;

    /**
     * @var UrlInterface
     */
    protected $url;

    /**
     * @var
     */
    protected $helper;

    /**
     * @var ObjectManagerInterface
     */
    protected $_objectManager;

    /**
     * @var UrlInterface
     */
    protected $_urlInterface;

    /**
     * @param ResponseFactory $responseFactory
     * @param UrlInterface $url
     * @param Session $customerSession
     */
    public function __construct(
        ResponseFactory $responseFactory,
        UrlInterface $url,
        Session $customerSession,
        Data $helper,
        ObjectManagerInterface $objectManager
    )
    {
        $this->responseFactory = $responseFactory;
        $this->url = $url;
        $this->customerSession = $customerSession;
        $this->dataHelper = $helper;
        $this->_objectManager = $objectManager;
    }

    public function execute(Observer $observer)
    {
        $restrictedProductIds = $this->dataHelper->getRestrictionProducts();
        $objectManager = \Magento\Framework\App\ObjectManager::getInstance();
        $product = $objectManager->get('Magento\Framework\Registry')->registry('current_product');//get current product
        $currentProductId = $product->getId();
        if (in_array($currentProductId, $restrictedProductIds)) {
            $url = $this->url->getUrl('404notfound');
            $observer->getControllerAction()
                ->getResponse()
                ->setRedirect($url);
        }
    }
}

