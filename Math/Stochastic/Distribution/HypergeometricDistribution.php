<?php
/**
 * Orange Management
 *
 * PHP Version 7.4
 *
 * @package   phpOMS\Math\Stochastic\Distribution
 * @copyright Dennis Eichhorn
 * @license   OMS License 1.0
 * @version   1.0.0
 * @link      https://orange-management.org
 */
declare(strict_types=1);
namespace phpOMS\Math\Stochastic\Distribution;

use phpOMS\Math\Functions\Functions;

/**
 * Hypergeometric distribution.
 *
 * @package phpOMS\Math\Stochastic\Distribution
 * @license OMS License 1.0
 * @link    https://orange-management.org
 * @since   1.0.0
 */
class HypergeometricDistribution
{
    /**
     * Get probability mass function.
     *
     * @param int $K Successful states in the population
     * @param int $N Population size
     * @param int $k Observed successes
     * @param int $n Number of draws
     *
     * @return float
     *
     * @todo: this can be heavily optimized
     *
     * @since 1.0.0
     */
    public static function getPmf(int $K, int $N, int $k, int $n) : float
    {
        return Functions::fact($K, $k) * Functions::fact($N - $K, $n - $k) / Functions::fact($N, $n);
    }

    /**
     * Get expected value.
     *
     * @param int $K Successful states in the population
     * @param int $N Population size
     * @param int $n Number of draws
     *
     * @return float
     *
     * @todo: this can be heavily optimized
     *
     * @since 1.0.0
     */
    public static function getMean(int $K, int $N, int $n) : float
    {
        return $n * $K / $N;
    }

    /**
     * Get mode.
     *
     * @param int $K Successful states in the population
     * @param int $N Population size
     * @param int $n Number of draws
     *
     * @return int
     *
     * @todo: this can be heavily optimized
     *
     * @since 1.0.0
     */
    public static function getMode(int $K, int $N, int $n) : int
    {
        return (int) (($n + 1) * ($K + 1) / ($N + 2));
    }

    /**
     * Get variance.
     *
     * @param int $K Successful states in the population
     * @param int $N Population size
     * @param int $n Number of draws
     *
     * @return float
     *
     * @todo: this can be heavily optimized
     *
     * @since 1.0.0
     */
    public static function getVariance(int $K, int $N, int $n) : float
    {
        return $n * $K / $N * ($N - $K) / $N * ($N - $n) / ($N - 1);
    }

    /**
     * Get standard deviation.
     *
     * @param int $K Successful states in the population
     * @param int $N Population size
     * @param int $n Number of draws
     *
     * @return float
     *
     * @todo: this can be heavily optimized
     *
     * @since 1.0.0
     */
    public static function getStandardDeviation(int $K, int $N, int $n) : float
    {
        return \sqrt(self::getVariance($K, $N, $n));
    }

    /**
     * Get skewness.
     *
     * @param int $K Successful states in the population
     * @param int $N Population size
     * @param int $n Number of draws
     *
     * @return float
     *
     * @todo: this can be heavily optimized
     *
     * @since 1.0.0
     */
    public static function getSkewness(int $K, int $N, int $n) : float
    {
        return ($N - 2 * $K) * \sqrt($N - 1) * ($N - 2 * $n)
            / (\sqrt($n * $K * ($N - $K) * ($N - $n)) * ($N - 2));
    }

    /**
     * Get Ex. kurtosis.
     *
     * @param int $K Successful states in the population
     * @param int $N Population size
     * @param int $n Number of draws
     *
     * @return float
     *
     * @todo: this can be heavily optimized
     *
     * @since 1.0.0
     */
    public static function getExKurtosis(int $K, int $N, int $n) : float
    {
        return (($N - 1) * $N ** 2 * ($N * ($N + 1) - 6 * $K * ($N - $K) - 6 * $n * ($N - $n)) + 6 * $n * $K * ($N - $K) * ($N - $n) * (5 * $N - 6))
            / ($n * $K * ($N - $K) * ($N - $n) * ($N - 2) * ($N - 3));
    }
}
