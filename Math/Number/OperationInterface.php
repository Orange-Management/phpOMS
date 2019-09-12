<?php
/**
 * Orange Management
 *
 * PHP Version 7.4
 *
 * @package   phpOMS\Math\Number
 * @copyright Dennis Eichhorn
 * @license   OMS License 1.0
 * @version   1.0.0
 * @link      https://orange-management.org
 */
declare(strict_types=1);

namespace phpOMS\Math\Number;

/**
 * Basic operation interface.
 *
 * @package phpOMS\Math\Number
 * @license OMS License 1.0
 * @link    https://orange-management.org
 * @since   1.0.0
 */
interface OperationInterface
{
    /**
     * Add value.
     *
     * @param mixed $x Value
     *
     * @return mixed
     *
     * @since 1.0.0
     */
    public function add($x);

    /**
     * Subtract value.
     *
     * @param mixed $x Value
     *
     * @return mixed
     *
     * @since 1.0.0
     */
    public function sub($x);

    /**
     * Right multiplicate value.
     *
     * @param mixed $x Value
     *
     * @return mixed
     *
     * @since 1.0.0
     */
    public function mult($x);

    /**
     * Right devision value.
     *
     * @param mixed $x Value
     *
     * @return mixed
     *
     * @since 1.0.0
     */
    public function div($x);

    /**
     * Power of value.
     *
     * @param mixed $p Power
     *
     * @return mixed
     *
     * @since 1.0.0
     */
    public function pow($p);

    /**
     * Abs of value.
     *
     * @return mixed
     *
     * @since 1.0.0
     */
    public function abs();
}
