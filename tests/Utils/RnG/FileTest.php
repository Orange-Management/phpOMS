<?php
/**
 * Orange Management
 *
 * PHP Version 7.2
 *
 * @package    tests
 * @copyright  Dennis Eichhorn
 * @license    OMS License 1.0
 * @version    1.0.0
 * @link       http://website.orange-management.de
 */

namespace phpOMS\tests\Utils\RnG;

use phpOMS\Utils\RnG\File;

class FileTest extends \PHPUnit\Framework\TestCase
{
    public function testRnGExtension() : void
    {
        self::assertRegExp('/^[a-z0-9]{1,5}$/', File::generateExtension());
    }
}
