<?php
/**
 * Orange Management
 *
 * PHP Version 7.4
 *
 * @package   phpOMS\Math\Numerics\Interpolation
 * @copyright Dennis Eichhorn
 * @license   OMS License 1.0
 * @version   1.0.0
 * @link      https://orange-management.org
 */
declare(strict_types=1);

namespace phpOMS\Math\Numerics\Interpolation;

/**
 * Interpolation interface.
 *
 * @package phpOMS\Math\Numerics\Interpolation
 * @license OMS License 1.0
 * @link    https://orange-management.org
 * @since   1.0.0
 */
interface InterpolationInterface
{
    /**
     * Interpolation at a given point
     *
     * @param int|float $x X-Coordinate to interpolate at
     *
     * @return float
     *
     * @since 1.0.0
     */
    public function interpolate(int|float $x) : float;
}
