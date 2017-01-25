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
namespace phpOMS\Utils\TaskSchedule;

/**
 * Cron class.
 *
 * @category   Framework
 * @package    phpOMS\Utils\TaskSchedule
 * @author     OMS Development Team <dev@oms.com>
 * @author     Dennis Eichhorn <d.eichhorn@oms.com>
 * @license    OMS License 1.0
 * @link       http://orange-management.com
 * @since      1.0.0
 */
class Cron extends SchedulerAbstract
{

    public function save()
    {

    }

    /**
     * Run command
     *
     * @param string $cmd Command to run
     *
     * @return array
     *
     * @since  1.0.0
     * @author Dennis Eichhorn
     */
    public function run(string $cmd) : array
    {
        // TODO: Implement run() method.
        return [];
    }
}
