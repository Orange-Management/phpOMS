<?php
/**
 * Orange Management
 *
 * PHP Version 7.1
 *
 * @category   TBD
 * @package    TBD
 * @author     OMS Development Team <dev@oms.com>
 * @author     Dennis Eichhorn <d.eichhorn@oms.com>
 * @copyright  Dennis Eichhorn
 * @license    OMS License 1.0
 * @version    1.0.0
 * @link       http://orange-management.com
 */
namespace phpOMS\Log;

use phpOMS\Datatypes\Enum;

/**
 * Log level enum.
 *
 * @category   Framework
 * @package    phpOMS\Log
 * @author     OMS Development Team <dev@oms.com>
 * @author     Dennis Eichhorn <d.eichhorn@oms.com>
 * @license    OMS License 1.0
 * @link       http://orange-management.com
 * @since      1.0.0
 */
abstract class LogLevel extends Enum
{
    /* public */ const EMERGENCY = 'emergency';
    /* public */ const ALERT = 'alert';
    /* public */ const CRITICAL = 'critical';
    /* public */ const ERROR = 'error';
    /* public */ const WARNING = 'warning';
    /* public */ const NOTICE = 'notice';
    /* public */ const INFO = 'info';
    /* public */ const DEBUG = 'debug';
}
