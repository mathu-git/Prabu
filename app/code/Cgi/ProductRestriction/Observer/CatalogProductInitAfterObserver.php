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
use Magento\Framework\Event\Observer;
use Magento\Framework\Event\ObserverInterface;
use Cgi\ProductRestriction\Helper\Data;
use Magento\Framework\UrlInterface;

/**
 * Class CatalogProductInitAfterObserver
 *
 * @package Cgi\ProductRestriction\Observer
 */
class CatalogProductInitAfterObserver implements ObserverInterface
{
    /**
     * Check the current customer Id
     *
     * @var Session
     */
    protected $customerSession;

    /**
     * Check the current url
     *
     * @var UrlInterface
     */
    protected $url;

    /**
     * Getting restricted product id from the helper
     *
     * @var dataHelper
     */
    protected $dataHelper;

    /**
     * Passing the parameter in the constructor
     *
     * @param UrlInterface $url             url
     * @param Session      $customerSession current customer id
     * @param Data         $dataHelper      product restriction Id
     */
    public function __construct(
        UrlInterface $url,
        Session $customerSession,
        Data $dataHelper
    ) {
        $this->url = $url;
        $this->customerSession = $customerSession;
        $this->dataHelper = $dataHelper;
    }

    /**
     * Check the product restricted id and update to the collection
     *
     * @param observer $observer Observer entity.
     *
     * @return $this void
     */
    public function execute(Observer $observer)
    {
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
