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
use Magento\Framework\UrlInterface;

/**
 * Class CatalogProductInitAfterObserver
 * @package Cgi\ProductRestriction\Observer
 */
class CatalogProductInitAfterObserver implements ObserverInterface
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
     * @param ResponseFactory $responseFactory
     * @param UrlInterface $url
     * @param Session $customerSession
     */
    public function __construct(
        ResponseFactory $responseFactory,
        UrlInterface $url,
        Session $customerSession,
        Data $helper
    )
    {
        $this->responseFactory = $responseFactory;
        $this->url = $url;
        $this->customerSession = $customerSession;
        $this->dataHelper = $helper;
    }

    /**
     * Execute observer.
     *
     * @param \Magento\Framework\Event\Observer $observer
     * @return $this
     */
    public function execute(Observer $observer)
    {
        $productIds = $this->dataHelper->getRestrictionProducts();
        $productCollection = $observer->getEvent()->getCollection();
        if ($productCollection) {
            $productCollection
                ->addAttributeToSelect('*')
                ->addAttributeToFilter('entity_id', array('nin' => $productIds));
        }
        return $this;
    }
}
