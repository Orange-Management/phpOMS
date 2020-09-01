<?php
/**
 * Orange Management
 *
 * PHP Version 7.4
 *
 * @package   phpOMS\DataStorage\Cache\Connection
 * @copyright Dennis Eichhorn
 * @license   OMS License 1.0
 * @version   1.0.0
 * @link      https://orange-management.org
 */
declare(strict_types=1);

namespace phpOMS\DataStorage\Cache\Connection;

use phpOMS\Stdlib\Base\Enum;

/**
 * Cache type enum.
 *
 * Possible caching types
 *
 * @package phpOMS\DataStorage\Cache\Connection
 * @license OMS License 1.0
 * @link    https://orange-management.org
 * @since   1.0.0
 */
abstract class CacheValueType extends Enum
{
    public const _INT = 0; /* Data is integer */

    public const _STRING = 1; /* Data is string */

    public const _ARRAY = 2; /* Data is array */

    public const _SERIALIZABLE = 3; /* Data implements \Serializable */

    public const _FLOAT = 4; /* Data is float */

    public const _BOOL = 5; /* Data is bool */

    public const _JSONSERIALIZABLE = 6; /* Data implements \JsonSerializable */

    public const _NULL = 7; /* Data is null */
}
