<?php
/**
 * Orange Management
 *
 * PHP Version 7.1
 *
 * @package    TBD
 * @copyright  Dennis Eichhorn
 * @license    OMS License 1.0
 * @version    1.0.0
 * @link       http://website.orange-management.de
 */

namespace phpOMS\tests\Module;

require_once __DIR__ . '/../Autoloader.php';

use phpOMS\Module\NullModule;
use phpOMS\ApplicationAbstract;

class NullModuleTest extends \PHPUnit\Framework\TestCase
{
    public function testModule()
    {
        $app = new class extends ApplicationAbstract
        {
        };

        self::assertInstanceOf('\phpOMS\Module\ModuleAbstract', new NullModule($app));
    }
}
