<?php
/**
 * *
 *  * Copyright © 2020 CGI. All rights reserved.
 *  * See COPYING.txt for license details.
 *  *
 *  * @author    CGI <info.de@cgi.com>
 *  * @copyright 2020 CGI
 *  * @license   http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 *
 */

namespace Cgi\ProductRestriction\Api\Data;
use Magento\Framework\Api\CustomAttributesDataInterface;
/**
 * @api
 * @since 100.1.0
 */
interface ConditionInterface extends CustomAttributesDataInterface
{
    /**#@+
     * Constants defined for keys of data array
     */
    const TYPE = 'type';

    const ATTRIBUTE = 'attribute';

    const OPERATOR = 'operator';

    const VALUE = 'value';

    const IS_VALUE_PARSED = 'is_value_parsed';

    const AGGREGATOR = 'aggregator';

    const CONDITIONS = 'conditions';
    /**#@-*/

    /**
     * @param string $type
     * @return $this
     * @since 100.1.0
     */
    public function setType($type);

    /**
     * @return string
     * @since 100.1.0
     */
    public function getType();

    /**
     * @param string $attribute
     * @return $this
     * @since 100.1.0
     */
    public function setAttribute($attribute);

    /**
     * @return string
     * @since 100.1.0
     */
    public function getAttribute();

    /**
     * @param string $operator
     * @return $this
     * @since 100.1.0
     */
    public function setOperator($operator);

    /**
     * @return string
     * @since 100.1.0
     */
    public function getOperator();

    /**
     * @param string $value
     * @return $this
     * @since 100.1.0
     */
    public function setValue($value);

    /**
     * @return string
     * @since 100.1.0
     */
    public function getValue();

    /**
     * @param bool $isValueParsed
     * @return $this
     * @since 100.1.0
     */
    public function setIsValueParsed($isValueParsed);

    /**
     * @return bool|null
     * @SuppressWarnings(PHPMD.BooleanGetMethodName)
     * @since 100.1.0
     */
    public function getIsValueParsed();

    /**
     * @param string $aggregator
     * @return $this
     * @since 100.1.0
     */
    public function setAggregator($aggregator);

    /**
     * @return string
     * @since 100.1.0
     */
    public function getAggregator();

    /**
     * @param \Cgi\ProductRestriction\Api\Data\ConditionInterface[] $conditions
     * @return $this
     * @since 100.1.0
     */
    public function setConditions($conditions);

    /**
     * @return \Cgi\ProductRestriction\Api\Data\ConditionInterface[]|null
     * @since 100.1.0
     */
    public function getConditions();

}
