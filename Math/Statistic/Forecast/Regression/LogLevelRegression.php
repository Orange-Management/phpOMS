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

namespace phpOMS\Math\Statistic\Forecast\Regression;

use phpOMS\Math\Matrix\Exception\InvalidDimensionException;

/**
 * Regression class.
 *
 * @package    Framework
 * @license    OMS License 1.0
 * @link       http://website.orange-management.de
 * @since      1.0.0
 */
class LogLevelRegression extends RegressionAbstract
{
    /**
     * {@inheritdoc}
     */
    public static function getRegression(array $x, array $y) : array
    {
        if (($c = count($x)) !== count($y)) {
            throw new InvalidDimensionException($c . 'x' . count($y));
        }

        for ($i = 0; $i < $c; ++$i) {
            $y[$i] = log($y[$i]);
        }

        return parent::getRegression($x, $y);
    }

    /**
     * {@inheritdoc}
     */
    public static function getSlope(float $b1, float $y, float $x) : float
    {
        return $b1 * $y;
    }

    /**
     * {@inheritdoc}
     */
    public static function getElasticity(float $b1, float $y, float $x): float
    {
        return $b1 * $x;
    }
}
