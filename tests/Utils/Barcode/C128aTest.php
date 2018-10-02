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

namespace phpOMS\tests\Utils\Barcode;

use phpOMS\Utils\Barcode\C128a;

class C128aTest extends \PHPUnit\Framework\TestCase
{
    public function testImage()
    {
        $path = __DIR__ . '/c128a.png';
        if (\file_exists($path)) {
            \unlink($path);
        }

        $img = new C128a('ABCDEFG0123()+-', 200, 50);
        $img->saveToPngFile($path);

        self::assertTrue(\file_exists($path));
    }

    public function testValidString()
    {
        self::assertTrue(C128a::isValidString('ABCDEFG0123+-'));
        self::assertFalse(C128a::isValidString('ABCDE~FG0123+-'));
    }
}
