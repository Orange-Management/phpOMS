<?php
/**
 * Orange Management
 *
 * PHP Version 7.2
 *
 * @package    phpOMS\Localization\Defaults
 * @copyright  Dennis Eichhorn
 * @license    OMS License 1.0
 * @version    1.0.0
 * @link       http://website.orange-management.de
 */
declare(strict_types=1);

namespace phpOMS\Localization\Defaults;

/**
 * City class.
 *
 * @package    phpOMS\Localization\Defaults
 * @license    OMS License 1.0
 * @link       http://website.orange-management.de
 * @since      1.0.0
 */
final class City
{
    /**
     * City id.
     *
     * @var int
     * @since 1.0.0
     */
    private $id = 0;

    /**
     * Country code.
     *
     * @var string
     * @since 1.0.0
     */
    private $countryCode = '';

    /**
     * State code.
     *
     * @var string
     * @since 1.0.0
     */
    private $state = '';

    /**
     * City name.
     *
     * @var string
     * @since 1.0.0
     */
    private $name = '';

    /**
     * Postal code.
     *
     * @var int
     * @since 1.0.0
     */
    private $postal = 0;

    /**
     * Latitude.
     *
     * @var float
     * @since 1.0.0
     */
    private $lat = 0.0;

    /**
     * Longitude.
     *
     * @var float
     * @since 1.0.0
     */
    private $long = 0.0;

    /**
     * Get city name
     *
     * @return string
     *
     * @since  1.0.0
     */
    public function getName() : string
    {
        return $this->name;
    }

    /**
     * Get country code
     *
     * @return string
     *
     * @since  1.0.0
     */
    public function getCountryCode() : string
    {
        return $this->countryCode;
    }

    /**
     * Get city state
     *
     * @return string
     *
     * @since  1.0.0
     */
    public function getState() : string
    {
        return $this->state;
    }

    /**
     * Get city postal
     *
     * @return int
     *
     * @since  1.0.0
     */
    public function getPostal() : int
    {
        return $this->postal;
    }

    /**
     * Get city latitude
     *
     * @return float
     *
     * @since  1.0.0
     */
    public function getLat() : float
    {
        return $this->lat;
    }

    /**
     * Get city longitude
     *
     * @return float
     *
     * @since  1.0.0
     */
    public function getLong() : float
    {
        return $this->long;
    }
}