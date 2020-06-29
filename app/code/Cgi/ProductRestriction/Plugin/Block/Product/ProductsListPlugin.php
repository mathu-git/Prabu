<?php
/**
 * Copyright © 2020 CGI. All rights reserved.
 * See COPYING.txt for license details.
 *
 * @author    CGI <info.de@cgi.com>
 * @copyright 2020 CGI
 * @license   http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

namespace Cgi\ProductRestriction\Plugin\Block\Product;

use Magento\Catalog\Model\ResourceModel\Product\Collection;
use Magento\CatalogWidget\Block\Product\ProductsList;
use Cgi\ProductRestriction\Helper\Data;
use Magento\Customer\Model\Session;
use Magento\Framework\Exception\NoSuchEntityException;

class ProductsListPlugin
{
    /**
     * @var Session
     */
    protected $customerSession;

    /**
     * @var
     */
    protected $helper;

    public function __construct(
        Session $customerSession,
        Data $helper
    )
    {
        $this->customerSession = $customerSession;
        $this->dataHelper = $helper;
    }

    /**
     * @param ProductsList $subject
     * @param Collection $result
     * @return Collection
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function afterCreateCollection(ProductsList $subject, Collection $result)
    {
        try {
            $productIds = $this->dataHelper->getRestrictionProducts();
            $result->addAttributeToSelect('*')
                ->addAttributeToFilter('entity_id', array('nin' => $productIds));
            return $result;
        } catch (NoSuchEntityException $e) {
            return [];
        }
    }
}


