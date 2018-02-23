<?php
/**
 * Orange Management
 *
 * PHP Version 7.1
 *
 * @package    TBD
 * @copyright  Dennis Eichhorn
 * @license    OMS License 1.0
 * @version    1.0.0
 * @link       http://website.orange-management.de
 */
declare(strict_types=1);

namespace phpOMS\Math\Statistic;

use phpOMS\Math\Exception\ZeroDevisionException;
use phpOMS\Math\Matrix\Exception\InvalidDimensionException;

/**
 * Measure of dispersion.
 *
 * @package    Framework
 * @license    OMS License 1.0
 * @link       http://website.orange-management.de
 * @since      1.0.0
 */
class MeasureOfDispersion
{

    /**
     * Get range.
     *
     * Example: ([4, 5, 9, 1, 3])
     *
     * @param array $values Values
     *
     * @return float
     *
     * @since  1.0.0
     */
    public static function range(array $values) : float
    {
        sort($values);
        $end   = end($values);
        $start = reset($values);

        return $end - $start;
    }

    /**
     * Calculage empirical variation coefficient.
     *
     * Example: ([4, 5, 9, 1, 3])
     *
     * @param array $values Values
     * @param float $mean   Mean
     *
     * @return float
     *
     * @throws \Exception
     *
     * @since  1.0.0
     */
    public static function empiricalVariationcoefficient(array $values, float $mean = null) : float
    {
        $mean = isset($mean) ? $mean : Average::arithmeticMean($values);

        if ($mean === 0) {
            throw new ZeroDevisionException();
        }

        return self::standardDeviation($values) / $mean;
    }

    /**
     * Calculage standard deviation.
     *
     * Example: ([4, 5, 9, 1, 3])
     *
     * @param array $values Values
     * @param float $mean   Mean
     *
     * @return float
     *
     * @since  1.0.0
     */
    public static function standardDeviation(array $values, float $mean = null) : float
    {
        $mean = isset($mean) ? $mean : Average::arithmeticMean($values);
        $sum  = 0.0;

        foreach ($values as $value) {
            $sum += ($value - $mean) ** 2;
        }

        return sqrt($sum / (count($values) - 1));
    }

    /**
     * Calculage sample variance.
     *
     * Example: ([4, 5, 9, 1, 3])
     *
     * @param array $values Values
     * @param float $mean   Mean
     *
     * @return float
     *
     * @throws \Exception
     *
     * @since  1.0.0
     */
    public static function sampleVariance(array $values, float $mean = null) : float
    {
        $count = count($values);

        if ($count < 2) {
            throw new ZeroDevisionException();
        }

        return self::empiricalVariance($values, [], $mean) * $count / ($count - 1);
    }

    /**
     * Calculage empirical variance.
     *
     * Example: ([4, 5, 9, 1, 3])
     *
     * @param array $values        Values
     * @param array $probabilities Probabilities
     * @param float $mean          Mean
     *
     * @return float
     *
     * @throws \Exception
     *
     * @since  1.0.0
     */
    public static function empiricalVariance(array $values, array $probabilities = [], float $mean = null) : float
    {
        $count          = count($values);
        $hasProbability = !empty($probabilities);

        if ($count === 0) {
            throw new ZeroDevisionException();
        }

        $mean = $hasProbability ? Average::weightedAverage($values, $probabilities) : (isset($mean) ? $mean : Average::arithmeticMean($values););
        $sum  = 0;

        foreach ($values as $key => $value) {
            $sum += ($hasProbability ? $probabilities[$key] : 1) * ($value - $mean) ** 2;
        }

        return $hasProbability ? $sum : $sum / $count;
    }

    /**
     * Calculage empirical covariance.
     *
     * Example: ([4, 5, 9, 1, 3], [4, 5, 9, 1, 3])
     *
     * @param array $x     Values
     * @param array $y     Values
     * @param array $meanX Mean
     * @param array $meanY Mean
     *
     * @return float
     *
     * @throws InvalidDimensionException
     *
     * @since  1.0.0
     */
    public static function empiricalCovariance(array $x, array $y, float $meanX = null, float $meanY = null) : float
    {
        $count = count($x);

        if ($count < 2) {
            throw new ZeroDevisionException();
        }

        if ($count !== count($y)) {
            throw new InvalidDimensionException($count . 'x' . count($y));
        }

        $xMean = isset($meanX) ? $meanX : Average::arithmeticMean($x);
        $yMean = isset($meanY) ? $meanY : Average::arithmeticMean($y);

        $sum = 0.0;

        for ($i = 0; $i < $count; ++$i) {
            $sum += ($x[$i] - $xMean) * ($y[$i] - $yMean);
        }

        return $sum / ($count - 1);
    }

    /**
     * Get interquartile range.
     *
     * @param array $x Dataset
     *
     * @return float
     *
     * @since  1.0.0
     */
    public static function getIQR(array $x) : float
    {
        return 0.0;
    }

    /**
     * Get mean deviation.
     *
     * @param array $x    Values
     * @param float $mean Mean
     *
     * @return float
     *
     * @since  1.0.0
     */
    public static function meanDeviation(array $x, float $mean = null) : float
    {
        $mean = isset($mean) ? $mean : Average::arithmeticMean($x);
        $sum  = 0.0;

        foreach ($x as $xi) {
            $sum += ($xi - $mean);
        }

        return $sum / count($x);
    }

    /**
     * Get mean absolute deviation.
     *
     * @param array $x    Values
     * @param float $mean Mean
     *
     * @return float
     *
     * @since  1.0.0
     */
    public static function meanAbsoluteDeviation(array $x, float $mean = null) : float
    {
        $mean = isset($mean) ? $mean : Average::arithmeticMean($x);
        $sum  = 0.0;

        foreach ($x as $xi) {
            $sum += abs($xi - $mean);
        }

        return $sum / count($x);
    }

    /**
     * Get squared mean deviation.
     *
     * @param array $x    Values
     * @param float $mean Mean
     *
     * @return float
     *
     * @since  1.0.0
     */
    public static function squaredMeanDeviation(array $x, float $mean = null) : float
    {
        $mean = isset($mean) ? $mean : Average::arithmeticMean($x);
        $sum  = 0.0;

        foreach ($x as $xi) {
            $sum += ($xi - $mean) ** 2;
        }

        return $sum / count($x);
    }
}
