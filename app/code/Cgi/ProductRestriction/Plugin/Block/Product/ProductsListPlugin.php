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

/**
 * Class ProductsListPlugin
 *
 * @package Cgi\ProductRestriction\Plugin\Block\Product
 */
class ProductsListPlugin
{
    /**
     * Customer Data
     *
     * @var Session
     */
    protected $customerSession;

    /**
     * Get data from rule id
     *
     * @var Data
     */
    protected $dataHelper;

    /**
     * ProductsListPlugin constructor.
     *
     * @param Session $customerSession
     * @param Data    $dataHelper
     */
    public function __construct(
        Session $customerSession,
        Data $dataHelper
    ) {
        $this->customerSession = $customerSession;
        $this->dataHelper = $dataHelper;
    }

    /**
     * Get the result after load the product collection
     *
     * @param                                         ProductsList $subject
     * @param                                         Collection   $result
     * @return                                        Collection
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function afterCreateCollection(ProductsList $subject, Collection $result)
    {
        $productIds = $this->dataHelper->getRestrictionProducts();
        $result->addAttributeToSelect('*')
            ->addAttributeToFilter('entity_id', array('nin' => $productIds));
        return $result;
    }
}
