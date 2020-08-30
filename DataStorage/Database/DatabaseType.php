<?php
/**
 * Orange Management
 *
 * PHP Version 7.4
 *
 * @package   phpOMS\DataStorage\Database
 * @copyright Dennis Eichhorn
 * @license   OMS License 1.0
 * @version   1.0.0
 * @link      https://orange-management.org
 */
declare(strict_types=1);

namespace phpOMS\DataStorage\Database;

use phpOMS\Stdlib\Base\Enum;

/**
 * Database type enum.
 *
 * Database types that are supported by the application
 *
 * @package phpOMS\DataStorage\Database
 * @license OMS License 1.0
 * @link    https://orange-management.org
 * @since   1.0.0
 */
abstract class DatabaseType extends Enum
{
    public const MYSQL     = 'mysql'; /* MySQL */

    public const SQLITE    = 'sqlite'; /* SQLITE */

    public const PGSQL     = 'pgsql'; /* PostgreSQL */

    public const SQLSRV    = 'mssql'; /* Microsoft SQL Server */

    public const UNDEFINED = 'undefined';
}
