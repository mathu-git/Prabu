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
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\UrlInterface;
use Magento\Catalog\Model\Product;
use Magento\Catalog\Model\ResourceModel\Product\Collection as ProductCollection;

/**
 * Class CatalogProductInitAfterObserver
 *
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
     * @var dataHelper
     */
    public $dataHelper;

    /**
     * @param ResponseFactory $responseFactory
     * @param UrlInterface $url
     * @param Session $customerSession
     * @param Data $dataHelper
     */
    public function __construct(
        ResponseFactory $responseFactory,
        UrlInterface $url,
        Session $customerSession,
        Data $dataHelper
    ) {
        $this->responseFactory = $responseFactory;
        $this->url = $url;
        $this->customerSession = $customerSession;
        $this->dataHelper = $dataHelper;
    }

    /**
     * Execute observer.
     *
     * @param \Magento\Framework\Event\Observer $observer
     * @return $this
     */
    public function execute(Observer $observer)
    {
        /** @var ProductCollection $collection */
        $newCollection = $observer->getEvent()->getCollection();
        $productIds = $this->dataHelper->getRestrictionProducts();
        if ($newCollection) {
            $newCollection
                ->addAttributeToSelect('*')
                ->addAttributeToFilter('entity_id', array('nin' => $productIds));
        }
        return $this;
    }
}
