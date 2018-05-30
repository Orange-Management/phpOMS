<?php
/**
 * Orange Management
 *
 * PHP Version 7.2
 *
 * @package    phpOMS\Message
 * @copyright  Dennis Eichhorn
 * @license    OMS License 1.0
 * @version    1.0.0
 * @link       http://website.orange-management.de
 */
declare(strict_types=1);

namespace phpOMS\Message;

use phpOMS\Stdlib\Base\Enum;

/**
 * Notification level enum.
 *
 * @package    phpOMS\Message
 * @license    OMS License 1.0
 * @link       http://website.orange-management.de
 * @since      1.0.0
 */
abstract class NotificationLevel extends Enum
{
    public const OK      = 'ok';
    public const WARNING = 'warning';
    public const ERROR   = 'error';
}
