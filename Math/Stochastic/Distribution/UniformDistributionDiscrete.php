<?php
/**
 * Orange Management
 *
 * PHP Version 7.1
 *
 * @category   TBD
 * @package    TBD
 * @author     OMS Development Team <dev@oms.com>
 * @author     Dennis Eichhorn <d.eichhorn@oms.com>
 * @copyright  Dennis Eichhorn
 * @license    OMS License 1.0
 * @version    1.0.0
 * @link       http://orange-management.com
 */

namespace phpOMS\Math\Stochastic\Distribution;

/**
 * Uniform (discrete) distribution.
 *
 * @category   Framework
 * @package    phpOMS\DataStorage\Database
 * @author     OMS Development Team <dev@oms.com>
 * @author     Dennis Eichhorn <d.eichhorn@oms.com>
 * @license    OMS License 1.0
 * @link       http://orange-management.com
 * @since      1.0.0
 */
class UniformDistributionDiscrete
{

    /**
     * Get probability mass function.
     *
     * @param float $a
     * @param float $b
     *
     * @return float
     *
     * @since  1.0.0
     * @author Dennis Eichhorn <d.eichhorn@oms.com>
     */
    public static function getPmf(float $a, float $b) : float
    {
        return 1 / ($b - $a + 1);
    }

    /**
     * Get cumulative distribution function.
     *
     * @param float $k
     * @param float $a
     * @param float $b
     *
     * @return float
     *
     * @throws \Exception
     *
     * @since  1.0.0
     * @author Dennis Eichhorn <d.eichhorn@oms.com>
     */
    public static function getCdf(float $k, float $a, float $b) : float
    {
        if ($k > $b || $k < $a) {
            throw new \Exception('Out of bounds');
        }

        return (floor($k) - $a + 1) / ($b - $a + 1);
    }

    /**
     * Get moment generating function.
     *
     * @param int   $t
     * @param float $a
     * @param float $b
     *
     * @return float
     *
     * @since  1.0.0
     * @author Dennis Eichhorn <d.eichhorn@oms.com>
     */
    public static function getMgf(int $t, float $a, float $b) : float
    {
        return (exp($a * $t) - exp(($b + 1) * $t)) / (($b - $a + 1) * (1 - exp($t)));
    }

    /**
     * Get skewness.
     *
     * @return float
     *
     * @since  1.0.0
     * @author Dennis Eichhorn <d.eichhorn@oms.com>
     */
    public static function getSkewness() : float
    {
        return 0.0;
    }

    /**
     * Get Ex. kurtosis.
     *
     * @param float $a
     * @param float $b
     *
     * @return float
     *
     * @since  1.0.0
     * @author Dennis Eichhorn <d.eichhorn@oms.com>
     */
    public static function getExKurtosis(float $a, float $b) : float
    {
        $n = ($b - $a + 1);

        return -6 / 5 * ($n ** 2 + 1) / ($n ** 2 - 1);
    }

    /**
     * Get expected value.
     *
     * @param float $a
     * @param float $b
     *
     * @return float
     *
     * @since  1.0.0
     * @author Dennis Eichhorn <d.eichhorn@oms.com>
     */
    public static function getMedian(float $a, float $b) : float
    {
        return ($a + $b) / 2;
    }

    /**
     * Get expected value.
     *
     * @param float $a
     * @param float $b
     *
     * @return float
     *
     * @since  1.0.0
     * @author Dennis Eichhorn <d.eichhorn@oms.com>
     */
    public static function getMean(float $a, float $b) : float
    {
        return ($a + $b) / 2;
    }

    /**
     * Get variance.
     *
     * @param float $a
     * @param float $b
     *
     * @return float
     *
     * @since  1.0.0
     * @author Dennis Eichhorn <d.eichhorn@oms.com>
     */
    public static function getVariance(float $a, float $b) : float
    {
        return (($b - $a + 1) ** 2 - 1) / 12;
    }

    public static function getRandom()
    {

    }
}
