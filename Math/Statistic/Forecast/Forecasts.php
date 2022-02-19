<?php
/**
 * Karaka
 *
 * PHP Version 8.0
 *
 * @package   phpOMS\Math\Statistic\Forecast
 * @copyright Dennis Eichhorn
 * @license   OMS License 1.0
 * @version   1.0.0
 * @link      https://karaka.app
 */
declare(strict_types=1);

namespace phpOMS\Math\Statistic\Forecast;

/**
 * General forecasts helper class.
 *
 * @package phpOMS\Math\Statistic\Forecast
 * @license OMS License 1.0
 * @link    https://karaka.app
 * @since   1.0.0
 */
final class Forecasts
{
    /**
     * Get forecast/prediction interval.
     *
     * @param float $forecast          Forecast value
     * @param float $standardDeviation Standard Deviation of forecast
     * @param float $interval          Forecast multiplier for prediction intervals
     *
     * @return array<int|float>
     *
     * @since 1.0.0
     */
    public static function getForecastInteval(float $forecast, float $standardDeviation, float $interval = 1.96) : array
    {
        return [$forecast - $interval * $standardDeviation, $forecast + $interval * $standardDeviation];
    }
}
