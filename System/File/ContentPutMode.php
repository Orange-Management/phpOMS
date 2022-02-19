<?php
/**
 * Karaka
 *
 * PHP Version 8.0
 *
 * @package   phpOMS\System\File
 * @copyright Dennis Eichhorn
 * @license   OMS License 1.0
 * @version   1.0.0
 * @link      https://karaka.app
 */
declare(strict_types=1);

namespace phpOMS\System\File;

use phpOMS\Stdlib\Base\Enum;

/**
 * Content put type enum.
 *
 * Defines how the content manipulation should be handled.
 *
 * @package phpOMS\System\File
 * @license OMS License 1.0
 * @link    https://karaka.app
 * @since   1.0.0
 */
abstract class ContentPutMode extends Enum
{
    public const APPEND = 1;

    public const PREPEND = 2;

    public const REPLACE = 4;

    public const CREATE = 8;
}
