<?php
/**
 * Orange Management
 *
 * PHP Version 7.4
 *
 * @package   tests
 * @copyright Dennis Eichhorn
 * @license   OMS License 1.0
 * @version   1.0.0
 * @link      https://orange-management.org
 */
 declare(strict_types=1);

namespace phpOMS\tests\Utils\TaskSchedule;

use phpOMS\Utils\TaskSchedule\CronJob;
use phpOMS\Utils\TaskSchedule\Schedule;
use phpOMS\Utils\TaskSchedule\TaskFactory;

/**
 * @internal
 */
class TaskFactoryTest extends \PHPUnit\Framework\TestCase
{
    public function testFactory() : void
    {
        self::assertTrue((TaskFactory::create('') instanceof CronJob) || (TaskFactory::create('') instanceof Schedule));
    }
}
