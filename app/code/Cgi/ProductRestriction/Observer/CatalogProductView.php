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

/**
 * Class CatalogProductView
 *
 * @package Cgi\ProductRestriction\Observer
 */
class CatalogProductView implements ObserverInterface
{
    /**
     * Response Factory
     *
     * @var ResponseFactory
     */
    protected $responseFactory;

    /**
     * Getting Current customer data
     *
     * @var Session
     */
    protected $customerSession;

    /**
     * Check the url
     *
     * @var UrlInterface
     */
    protected $url;

    /**
     * Register Factory
     *
     * @var Registry
     */
    protected $registry;

    /**
     * Get Restricted Product ids
     *
     * @var Data
     */
    public $dataHelper;

    /**
     * Passing the parameter in the constructor
     *
     * @param ResponseFactory $responseFactory Response results
     * @param UrlInterface    $url             url
     * @param Session         $customerSession customer data
     * @param Data            $dataHelper      Getting product id
     * @param Registry        $registry        Registry
     */
    public function __construct(
        ResponseFactory $responseFactory,
        UrlInterface $url,
        Session $customerSession,
        Data $dataHelper,
        Registry $registry
    ) {
        $this->responseFactory = $responseFactory;
        $this->url = $url;
        $this->customerSession = $customerSession;
        $this->dataHelper = $dataHelper;
        $this->registry = $registry;
    }

    /**
     *  Check the product restricted id and update to the collection in Home page
     *
     * @param Observer $observer Observer entity.
     */
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
