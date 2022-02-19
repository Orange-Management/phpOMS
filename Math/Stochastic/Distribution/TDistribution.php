<?php
/**
 * Karaka
 *
 * PHP Version 8.0
 *
 * @package   phpOMS\Math\Stochastic\Distribution
 * @copyright Dennis Eichhorn
 * @license   OMS License 1.0
 * @version   1.0.0
 * @link      https://karaka.app
 */
declare(strict_types=1);

namespace phpOMS\Math\Stochastic\Distribution;

/**
 * T distribution.
 *
 * Test the similarity of two means
 *
 * @package phpOMS\Math\Stochastic\Distribution
 * @license OMS License 1.0
 * @link    https://karaka.app
 * @since   1.0.0
 */
final class TDistribution
{
    /**
     * T table.
     *
     * [degrees of freedom = df]
     *
     * @var array<int, array<string, float>>
     * @since 1.0.0
     */
    public const TABLE = [
        1    => ['0' => 0.000, '0.5' => 1.000, '0.6' => 1.376, '0.7' => 1.963, '0.8' => 3.078, '0.9' => 6.314, '0.95' => 12.71, '0.98' => 31.82, '0.99' => 63.66, '0.998' => 318.31, '0.999' => 636.62,],
        2    => ['0' => 0.000, '0.5' => 0.816, '0.6' => 1.061, '0.7' => 1.386, '0.8' => 1.886, '0.9' => 2.920, '0.95' => 4.303, '0.98' => 6.965, '0.99' => 9.925, '0.998' => 22.327, '0.999' => 31.599,],
        3    => ['0' => 0.000, '0.5' => 0.765, '0.6' => 0.978, '0.7' => 1.250, '0.8' => 1.638, '0.9' => 2.353, '0.95' => 3.182, '0.98' => 4.541, '0.99' => 5.841, '0.998' => 10.215, '0.999' => 12.924,],
        4    => ['0' => 0.000, '0.5' => 0.741, '0.6' => 0.941, '0.7' => 1.190, '0.8' => 1.533, '0.9' => 2.132, '0.95' => 2.776, '0.98' => 3.747, '0.99' => 4.604, '0.998' => 7.173, '0.999' => 8.610,],
        5    => ['0' => 0.000, '0.5' => 0.727, '0.6' => 0.920, '0.7' => 1.156, '0.8' => 1.476, '0.9' => 2.015, '0.95' => 2.571, '0.98' => 3.365, '0.99' => 4.032, '0.998' => 5.893, '0.999' => 6.869,],
        6    => ['0' => 0.000, '0.5' => 0.718, '0.6' => 0.906, '0.7' => 1.134, '0.8' => 1.440, '0.9' => 1.943, '0.95' => 2.447, '0.98' => 3.143, '0.99' => 3.707, '0.998' => 5.208, '0.999' => 5.959,],
        7    => ['0' => 0.000, '0.5' => 0.711, '0.6' => 0.896, '0.7' => 1.119, '0.8' => 1.415, '0.9' => 1.895, '0.95' => 2.365, '0.98' => 2.998, '0.99' => 3.499, '0.998' => 4.785, '0.999' => 5.408,],
        8    => ['0' => 0.000, '0.5' => 0.706, '0.6' => 0.889, '0.7' => 1.108, '0.8' => 1.397, '0.9' => 1.860, '0.95' => 2.306, '0.98' => 2.896, '0.99' => 3.355, '0.998' => 4.501, '0.999' => 5.041,],
        9    => ['0' => 0.000, '0.5' => 0.703, '0.6' => 0.883, '0.7' => 1.100, '0.8' => 1.383, '0.9' => 1.833, '0.95' => 2.262, '0.98' => 2.821, '0.99' => 3.250, '0.998' => 4.297, '0.999' => 4.781,],
        10   => ['0' => 0.000, '0.5' => 0.700, '0.6' => 0.879, '0.7' => 1.093, '0.8' => 1.372, '0.9' => 1.812, '0.95' => 2.228, '0.98' => 2.764, '0.99' => 3.169, '0.998' => 4.144, '0.999' => 4.587,],
        11   => ['0' => 0.000, '0.5' => 0.697, '0.6' => 0.876, '0.7' => 1.088, '0.8' => 1.363, '0.9' => 1.796, '0.95' => 2.201, '0.98' => 2.718, '0.99' => 3.106, '0.998' => 4.025, '0.999' => 4.437,],
        12   => ['0' => 0.000, '0.5' => 0.695, '0.6' => 0.873, '0.7' => 1.083, '0.8' => 1.356, '0.9' => 1.782, '0.95' => 2.179, '0.98' => 2.681, '0.99' => 3.055, '0.998' => 3.930, '0.999' => 4.318,],
        13   => ['0' => 0.000, '0.5' => 0.694, '0.6' => 0.870, '0.7' => 1.079, '0.8' => 1.350, '0.9' => 1.771, '0.95' => 2.160, '0.98' => 2.650, '0.99' => 3.012, '0.998' => 3.852, '0.999' => 4.221,],
        14   => ['0' => 0.000, '0.5' => 0.692, '0.6' => 0.868, '0.7' => 1.076, '0.8' => 1.345, '0.9' => 1.761, '0.95' => 2.145, '0.98' => 2.624, '0.99' => 2.977, '0.998' => 3.787, '0.999' => 4.140,],
        15   => ['0' => 0.000, '0.5' => 0.691, '0.6' => 0.866, '0.7' => 1.074, '0.8' => 1.341, '0.9' => 1.753, '0.95' => 2.131, '0.98' => 2.602, '0.99' => 2.947, '0.998' => 3.733, '0.999' => 4.073,],
        16   => ['0' => 0.000, '0.5' => 0.690, '0.6' => 0.865, '0.7' => 1.071, '0.8' => 1.337, '0.9' => 1.746, '0.95' => 2.120, '0.98' => 2.583, '0.99' => 2.921, '0.998' => 3.686, '0.999' => 4.015,],
        17   => ['0' => 0.000, '0.5' => 0.689, '0.6' => 0.863, '0.7' => 1.069, '0.8' => 1.333, '0.9' => 1.740, '0.95' => 2.110, '0.98' => 2.567, '0.99' => 2.898, '0.998' => 3.646, '0.999' => 3.965,],
        18   => ['0' => 0.000, '0.5' => 0.688, '0.6' => 0.862, '0.7' => 1.067, '0.8' => 1.330, '0.9' => 1.734, '0.95' => 2.101, '0.98' => 2.552, '0.99' => 2.878, '0.998' => 3.610, '0.999' => 3.922,],
        19   => ['0' => 0.000, '0.5' => 0.688, '0.6' => 0.861, '0.7' => 1.066, '0.8' => 1.328, '0.9' => 1.729, '0.95' => 2.093, '0.98' => 2.539, '0.99' => 2.861, '0.998' => 3.579, '0.999' => 3.883,],
        20   => ['0' => 0.000, '0.5' => 0.687, '0.6' => 0.860, '0.7' => 1.064, '0.8' => 1.325, '0.9' => 1.725, '0.95' => 2.086, '0.98' => 2.528, '0.99' => 2.845, '0.998' => 3.552, '0.999' => 3.850,],
        21   => ['0' => 0.000, '0.5' => 0.686, '0.6' => 0.859, '0.7' => 1.063, '0.8' => 1.323, '0.9' => 1.721, '0.95' => 2.080, '0.98' => 2.518, '0.99' => 2.831, '0.998' => 3.527, '0.999' => 3.819,],
        22   => ['0' => 0.000, '0.5' => 0.686, '0.6' => 0.858, '0.7' => 1.061, '0.8' => 1.321, '0.9' => 1.717, '0.95' => 2.074, '0.98' => 2.508, '0.99' => 2.819, '0.998' => 3.505, '0.999' => 3.792,],
        23   => ['0' => 0.000, '0.5' => 0.685, '0.6' => 0.858, '0.7' => 1.060, '0.8' => 1.319, '0.9' => 1.714, '0.95' => 2.069, '0.98' => 2.500, '0.99' => 2.807, '0.998' => 3.485, '0.999' => 3.768,],
        24   => ['0' => 0.000, '0.5' => 0.685, '0.6' => 0.857, '0.7' => 1.059, '0.8' => 1.318, '0.9' => 1.711, '0.95' => 2.064, '0.98' => 2.492, '0.99' => 2.797, '0.998' => 3.467, '0.999' => 3.745,],
        25   => ['0' => 0.000, '0.5' => 0.684, '0.6' => 0.856, '0.7' => 1.058, '0.8' => 1.316, '0.9' => 1.708, '0.95' => 2.060, '0.98' => 2.485, '0.99' => 2.787, '0.998' => 3.450, '0.999' => 3.725,],
        26   => ['0' => 0.000, '0.5' => 0.684, '0.6' => 0.856, '0.7' => 1.058, '0.8' => 1.315, '0.9' => 1.706, '0.95' => 2.056, '0.98' => 2.479, '0.99' => 2.779, '0.998' => 3.435, '0.999' => 3.707,],
        27   => ['0' => 0.000, '0.5' => 0.684, '0.6' => 0.855, '0.7' => 1.057, '0.8' => 1.314, '0.9' => 1.703, '0.95' => 2.052, '0.98' => 2.473, '0.99' => 2.771, '0.998' => 3.421, '0.999' => 3.690,],
        28   => ['0' => 0.000, '0.5' => 0.683, '0.6' => 0.855, '0.7' => 1.056, '0.8' => 1.313, '0.9' => 1.701, '0.95' => 2.048, '0.98' => 2.467, '0.99' => 2.763, '0.998' => 3.408, '0.999' => 3.674,],
        29   => ['0' => 0.000, '0.5' => 0.683, '0.6' => 0.854, '0.7' => 1.055, '0.8' => 1.311, '0.9' => 1.699, '0.95' => 2.045, '0.98' => 2.462, '0.99' => 2.756, '0.998' => 3.396, '0.999' => 3.659,],
        30   => ['0' => 0.000, '0.5' => 0.683, '0.6' => 0.854, '0.7' => 1.055, '0.8' => 1.310, '0.9' => 1.697, '0.95' => 2.042, '0.98' => 2.457, '0.99' => 2.750, '0.998' => 3.385, '0.999' => 3.646,],
        40   => ['0' => 0.000, '0.5' => 0.681, '0.6' => 0.851, '0.7' => 1.050, '0.8' => 1.303, '0.9' => 1.684, '0.95' => 2.021, '0.98' => 2.423, '0.99' => 2.704, '0.998' => 3.307, '0.999' => 3.551,],
        60   => ['0' => 0.000, '0.5' => 0.679, '0.6' => 0.848, '0.7' => 1.045, '0.8' => 1.296, '0.9' => 1.671, '0.95' => 2.000, '0.98' => 2.390, '0.99' => 2.660, '0.998' => 3.232, '0.999' => 3.460,],
        80   => ['0' => 0.000, '0.5' => 0.678, '0.6' => 0.846, '0.7' => 1.043, '0.8' => 1.292, '0.9' => 1.664, '0.95' => 1.990, '0.98' => 2.374, '0.99' => 2.639, '0.998' => 3.195, '0.999' => 3.416,],
        100  => ['0' => 0.000, '0.5' => 0.677, '0.6' => 0.845, '0.7' => 1.042, '0.8' => 1.290, '0.9' => 1.660, '0.95' => 1.984, '0.98' => 2.364, '0.99' => 2.626, '0.998' => 3.174, '0.999' => 3.390,],
        1000 => ['0' => 0.000, '0.5' => 0.675, '0.6' => 0.842, '0.7' => 1.037, '0.8' => 1.282, '0.9' => 1.646, '0.95' => 1.962, '0.98' => 2.330, '0.99' => 2.581, '0.998' => 3.098, '0.999' => 3.300,],
    ];

    /**
     * Get expected value.
     *
     * @return int
     *
     * @since 1.0.0
     */
    public static function getMean() : int
    {
        return 0;
    }

    /**
     * Get median.
     *
     * @return int
     *
     * @since 1.0.0
     */
    public static function getMedian() : int
    {
        return 0;
    }

    /**
     * Get mode.
     *
     * @return int
     *
     * @since 1.0.0
     */
    public static function getMode() : int
    {
        return 0;
    }

    /**
     * Get skewness.
     *
     * @return int
     *
     * @since 1.0.0
     */
    public static function getSkewness() : int
    {
        return 0;
    }

    /**
     * Get variance.
     *
     * @param int $nu Degrees of freedom
     *
     * @return float
     *
     * @since 1.0.0
     */
    public static function getVariance(int $nu) : float
    {
        return $nu < 3 ? \PHP_FLOAT_MAX : $nu / ($nu - 2);
    }

    /**
     * Get standard deviation.
     *
     * @param int $nu Degrees of freedom
     *
     * @return float
     *
     * @since 1.0.0
     */
    public static function getStandardDeviation(int $nu) : float
    {
        return $nu < 3 ? \PHP_FLOAT_MAX : \sqrt(self::getVariance($nu));
    }

    /**
     * Get Ex. kurtosis.
     *
     * @param float $nu Degrees of freedom
     *
     * @return float
     *
     * @since 1.0.0
     */
    public static function getExKurtosis(float $nu) : float
    {
        return $nu < 5 && $nu > 2 ? \PHP_FLOAT_MAX : 6 / ($nu - 4);
    }

    /**
     * Get cumulative distribution function.
     *
     * @param float $x       Value
     * @param int   $degrees Degrees of freedom
     * @param int   $tails   Tails (1 or 2)
     *
     * @return float
     *
     * @since 1.0.0
     */
    public static function getCdf(float $x, int $degrees, int $tails = 1) : float
    {
        if ($x < 0.0 || $degrees < 1 || $tails < 1 || $tails > 2) {
            return 0.0;
        }

        /**
         * "AS 3" by B E Cooper of the Atlas Computer Laboratory
         * Ellis Horwood Ltd.; W. Sussex, England
         */
        $term  = $degrees;
        $theta = \atan2($x, \sqrt($term));
        $cos   = \cos($theta);
        $sin   = \sin($theta);
        $sum   = 0.0;

        if ($degrees % 2 === 1) {
            $i    = 3;
            $term = $cos;
        } else {
            $i    = 2;
            $term = 1;
        }

        $sum = $term;
        while ($i < $degrees) {
            $term *= $cos ** 2 * ($i - 1) / $i;
            $sum  += $term;
            $i    += 2;
        }

        $sum *= $sin;

        if ($degrees % 2 === 1) {
            $sum = 2 / \M_PI * ($sum + $theta);
        }

        $t = 0.5 * (1 + $sum);

        return $tails === 1 ? \abs($t) : 1 - \abs(1 - $t - $t);
    }
}
