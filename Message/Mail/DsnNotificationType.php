<?php
/**
 * Karaka
 *
 * PHP Version 8.0
 *
 * @package   phpOMS\Message\Mail
 * @copyright Dennis Eichhorn
 * @license   OMS License 1.0
 * @version   1.0.0
 * @link      https://karaka.app
 */
declare(strict_types=1);

namespace phpOMS\Message\Mail;

use phpOMS\Stdlib\Base\Enum;

/**
 * Dsn notification types enum.
 *
 * @package phpOMS\Message\Mail
 * @license OMS License 1.0
 * @link    https://karaka.app
 * @since   1.0.0
 */
abstract class DsnNotificationType extends Enum
{
    public const NONE = '';

    public const NEVER = 'NEVER';

    public const SUCCESS = 'SUCCESS';

    public const FAILURE = 'FAILURE';

    public const DELAY = 'DELAY';
}
