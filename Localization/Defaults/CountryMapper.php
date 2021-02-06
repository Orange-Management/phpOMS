<?php
/**
 * Orange Management
 *
 * PHP Version 8.0
 *
 * @package   phpOMS\Localization\Defaults
 * @copyright Dennis Eichhorn
 * @license   OMS License 1.0
 * @version   1.0.0
 * @link      https://orange-management.org
 */
declare(strict_types=1);

namespace phpOMS\Localization\Defaults;

use phpOMS\DataStorage\Database\DataMapperAbstract;

/**
 * Mapper class.
 *
 * @package phpOMS\Localization\Defaults
 * @license OMS License 1.0
 * @link    https://orange-management.org
 * @since   1.0.0
 */
class CountryMapper extends DataMapperAbstract
{
    /**
     * Columns.
     *
     * @var array<string, array{name:string, type:string, internal:string, autocomplete?:bool, readonly?:bool, writeonly?:bool, annotations?:array}>
     * @since 1.0.0
     */
    protected static array $columns = [
        'country_id'        => ['name' => 'country_id',      'type' => 'int',    'internal' => 'id'],
        'country_name'      => ['name' => 'country_name',    'type' => 'string', 'internal' => 'name'],
        'country_code2'     => ['name' => 'country_code2',   'type' => 'string', 'internal' => 'code2'],
        'country_code3'     => ['name' => 'country_code3',   'type' => 'string', 'internal' => 'code3'],
        'country_numeric'   => ['name' => 'country_numeric', 'type' => 'int',    'internal' => 'numeric'],
        'country_region'    => ['name' => 'country_region', 'type' => 'string',    'internal' => 'region'],
        'country_developed' => ['name' => 'country_developed', 'type' => 'bool',    'internal' => 'isDeveloped'],
    ];

    /**
     * Primary table.
     *
     * @var string
     * @since 1.0.0
     */
    protected static string $table = 'country';

    /**
     * Primary field name.
     *
     * @var string
     * @since 1.0.0
     */
    protected static string $primaryField = 'country_id';
}
