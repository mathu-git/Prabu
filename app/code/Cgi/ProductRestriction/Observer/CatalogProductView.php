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
use Magento\Framework\Registry;
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
     * @var $helper
     */
    protected $helper;

    public $registry;

    /**
     * @var Data
     */
    public $dataHelper;

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
        Registry $registry
    )
    {
        $this->responseFactory = $responseFactory;
        $this->url = $url;
        $this->customerSession = $customerSession;
        $this->dataHelper = $helper;
        $this->registry = $registry;
    }

    public function execute(Observer $observer)
    {
        $restrictedProductIds = $this->dataHelper->getRestrictionProducts();
        $product = $this->registry->registry('current_product');
        $currentProductId = $product->getId();
        if (in_array($currentProductId, $restrictedProductIds)) {
            $url = $this->url->getUrl('404notfound');
            $observer->getControllerAction()
                ->getResponse()
                ->setRedirect($url);
        }
    }
}

