<?php
/**
 * Orange Management
 *
 * PHP Version 7.4
 *
 * @package   phpOMS\Utils\Converter
 * @copyright Dennis Eichhorn
 * @license   OMS License 1.0
 * @version   1.0.0
 * @link      https://orange-management.org
 */
declare(strict_types=1);

namespace phpOMS\Utils\Converter;

use phpOMS\Stdlib\Base\Enum;

/**
 * File size type enum.
 *
 * @package phpOMS\Utils\Converter
 * @license OMS License 1.0
 * @link    https://orange-management.org
 * @since   1.0.0
 */
abstract class FileSizeType extends Enum
{
    public const TERRABYTE = 'TB';

    public const GIGABYTE  = 'GB';

    public const MEGABYTE  = 'MB';

    public const KILOBYTE  = 'KB';

    public const BYTE      = 'B';

    public const TERRABIT  = 'tbit';

    public const GIGABIT   = 'gbit';

    public const MEGABIT   = 'mbit';

    public const KILOBIT   = 'kbit';

    public const BIT       = 'bit';
}
