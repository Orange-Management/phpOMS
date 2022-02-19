<?php
/**
 * Karaka
 *
 * PHP Version 8.0
 *
 * @package   phpOMS\Message\Http
 * @copyright Dennis Eichhorn
 * @license   OMS License 1.0
 * @version   1.0.0
 * @link      https://karaka.app
 */
declare(strict_types=1);

namespace phpOMS\Message\Http;

use phpOMS\Stdlib\Base\Enum;

/**
 * Browser type enum.
 *
 * Browser types can be used for statistics or in order to deliver browser specific content.
 *
 * @package phpOMS\Message\Http
 * @license OMS License 1.0
 * @link    https://karaka.app
 * @since   1.0.0
 */
abstract class BrowserType extends Enum
{
    public const IE = 'msie'; /* Internet Explorer */

    public const EDGE = 'edge'; /* Internet Explorer Edge 20+ */

    public const FIREFOX = 'firefox'; /* Firefox */

    public const SAFARI = 'safari'; /* Safari */

    public const CHROME = 'chrome'; /* Chrome */

    public const OPERA = 'opera'; /* Opera */

    public const NETSCAPE = 'netscape'; /* Netscape */

    public const MAXTHON = 'maxthon'; /* Maxthon */

    public const KONQUEROR = 'konqueror'; /* Konqueror */

    public const HANDHELD = 'mobile'; /* Handheld Browser */

    public const BLINK = 'blink'; /* Blink Browser */

    public const UNKNOWN = 'unknown';
}
