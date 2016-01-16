<?php
/**
 * Orange Management
 *
 * PHP Version 7.0
 *
 * @category   TBD
 * @package    TBD
 * @author     OMS Development Team <dev@oms.com>
 * @author     Dennis Eichhorn <d.eichhorn@oms.com>
 * @copyright  2013 Dennis Eichhorn
 * @license    OMS License 1.0
 * @version    1.0.0
 * @link       http://orange-management.com
 */

namespace phpOMS\DataStorage\Database;

abstract class GrammarAbstract
{
    /**
     * Comment style.
     *
     * @var \string
     * @since 1.0.0
     */
    protected $comment = '--';

    /**
     * String quotes style.
     *
     * @var \string
     * @since 1.0.0
     */
    protected $valueQuotes = '\'';

    /**
     * System identifier.
     *
     * @var \string
     * @since 1.0.0
     */
    protected $systemIdentifier = '"';

    /**
     * And operator.
     *
     * @var \string
     * @since 1.0.0
     */
    protected $and = 'AND';

    /**
     * Or operator.
     *
     * @var \string
     * @since 1.0.0
     */
    protected $or = 'OR';

    protected $tablePrefix = '';

    /**
     * Compile to query.
     *
     * @param BuilderAbstract $query Builder
     *
     * @return \string
     *
     * @throws
     *
     * @since  1.0.0
     * @author Dennis Eichhorn <d.eichhorn@oms.com>
     */
    public function compileQuery(BuilderAbstract $query) : \string
    {
        return trim(
            implode(' ',
                array_filter(
                    $this->compileComponents($query),
                    function ($value) {
                        return (string) $value !== '';
                    }
                )
            )
        ) . ';';
    }

    abstract protected function compileComponents(BuilderAbstract $query) : array;

    /**
     * Expressionize elements.
     *
     * @param array   $elements Elements
     * @param \string $prefix   Prefix for table
     *
     * @return \string
     *
     * @since  1.0.0
     * @author Dennis Eichhorn <d.eichhorn@oms.com>
     */
    protected function expressionizeTableColumn(array $elements, \string $prefix = '') : \string
    {
        $expression = '';

        foreach ($elements as $key => $element) {
            if (is_string($element) && $element !== '*') {
                $expression .= $this->compileSystem($element, $prefix) . ', ';
            } elseif (is_string($element) && $element === '*') {
                $expression .= '*, ';
            } elseif ($element instanceof \Closure) {
                $expression .= $element() . ', ';
            } elseif ($element instanceof BuilderAbstract) {
                $expression .= $element->toSql() . ', ';
            } else {
                throw new \InvalidArgumentException();
            }
        }

        return rtrim($expression, ', ');
    }

    /**
     * Compile system.
     *
     * @param array|\string $system System
     * @param \string       $prefix Prefix for table
     *
     * @return \string
     *
     * @since  1.0.0
     * @author Dennis Eichhorn <d.eichhorn@oms.com>
     */
    protected function compileSystem($system, \string $prefix = '') : \string
    {
        if (count($split = explode('.', $system)) == 2) {
            return $this->compileSystem($prefix . $split[0]) . '.' . $this->compileSystem($split[1]);
        } else {
            return $this->systemIdentifier . $prefix . $system . $this->systemIdentifier;
        }
    }

    public function getDateFormat() : \string
    {
        return 'Y-m-d H:i:s';
    }

    public function getTablePrefix() : \string
    {
        return $this->tablePrefix;
    }

    public function setTablePrefix(\string $prefix)
    {
        $this->tablePrefix = $prefix;
    }

}
