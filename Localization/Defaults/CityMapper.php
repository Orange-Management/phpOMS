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

use phpOMS\DataStorage\Database\DataMapperAbstract;
use phpOMS\DataStorage\Database\Query\Builder;
use phpOMS\DataStorage\Database\Query\Column;
use phpOMS\DataStorage\Database\RelationType;

/**
 * Mapper class.
 *
 * @package    phpOMS\Localization\Defaults
 * @license    OMS License 1.0
 * @link       http://website.orange-management.de
 * @since      1.0.0
 */
class CityMapper extends DataMapperAbstract
{

    /**
     * Columns.
     *
     * @var array<string, array<string, string>>
     * @since 1.0.0
     */
    protected static $columns = [
        'city_id'      => ['name' => 'city_id', 'type' => 'int', 'internal' => 'id'],
        'city_city'    => ['name' => 'city_city', 'type' => 'string', 'internal' => 'name'],
        'city_country' => ['name' => 'city_country', 'type' => 'string', 'internal' => 'countryCode'],
        'city_state'   => ['name' => 'city_state', 'type' => 'string', 'internal' => 'state'],
        'city_postal'  => ['name' => 'city_postal', 'type' => 'int', 'internal' => 'postal'],
        'city_lat'     => ['name' => 'city_lat', 'type' => 'float', 'internal' => 'lat'],
        'city_long'    => ['name' => 'city_long', 'type' => 'float', 'internal' => 'long'],
    ];

    /**
     * Primary table.
     *
     * @var string
     * @since 1.0.0
     */
    protected static $table = 'city';

    /**
     * Primary field name.
     *
     * @var string
     * @since 1.0.0
     */
    protected static $primaryField = 'city_id';
}