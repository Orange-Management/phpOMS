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

namespace phpOMS\tests\Utils\IO\Zip;

use phpOMS\Utils\IO\Zip\Zip;

/**
 * @internal
 */
class ZipTest extends \PHPUnit\Framework\TestCase
{
    protected function setUp() : void
    {
        if (!\extension_loaded('zip')) {
            $this->markTestSkipped(
              'The ZIP extension is not available.'
            );
        }
    }

    public function testZip() : void
    {
        self::assertTrue(Zip::pack(
            [
                __DIR__ . '/test a.txt' => 'test a.txt',
                __DIR__ . '/test b.md' => 'test b.md',
                __DIR__ . '/test' => 'test',
            ],
            __DIR__ . '/test.zip'
        ));

        self::assertFileExists(__DIR__ . '/test.zip');

        self::assertFalse(Zip::pack(
            [
                __DIR__ . '/test a.txt' => 'test a.txt',
                __DIR__ . '/test b.txt' => 'test b.txt',
            ],
            __DIR__ . '/test.zip',
            false
        ));

        $a = \file_get_contents(__DIR__ . '/test a.txt');
        $b = \file_get_contents(__DIR__ . '/test b.md');
        $c = \file_get_contents(__DIR__ . '/test/test c.txt');
        $d = \file_get_contents(__DIR__ . '/test/test d.txt');
        $e = \file_get_contents(__DIR__ . '/test/sub/test e.txt');

        \unlink(__DIR__ . '/test a.txt');
        \unlink(__DIR__ . '/test b.md');
        \unlink(__DIR__ . '/test/test c.txt');
        \unlink(__DIR__ . '/test/test d.txt');
        \unlink(__DIR__ . '/test/sub/test e.txt');
        \rmdir(__DIR__ . '/test/sub');
        \rmdir(__DIR__ . '/test');

        self::assertFileNotExists(__DIR__ . '/test a.txt');
        self::assertFileNotExists(__DIR__ . '/test b.md');
        self::assertFileNotExists(__DIR__ . '/test/test c.txt');
        self::assertFileNotExists(__DIR__ . '/test/test d.txt');
        self::assertFileNotExists(__DIR__ . '/test/sub/test e.txt');
        self::assertFileNotExists(__DIR__ . '/test/sub');
        self::assertFileNotExists(__DIR__ . '/test');

        self::assertTrue(Zip::unpack(__DIR__ . '/test.zip', __DIR__));

        self::assertFileExists(__DIR__ . '/test a.txt');
        self::assertFileExists(__DIR__ . '/test b.md');
        self::assertFileExists(__DIR__ . '/test/test c.txt');
        self::assertFileExists(__DIR__ . '/test/test d.txt');
        self::assertFileExists(__DIR__ . '/test/sub/test e.txt');
        self::assertFileExists(__DIR__ . '/test/sub');
        self::assertFileExists(__DIR__ . '/test');

        self::assertEquals($a, \file_get_contents(__DIR__ . '/test a.txt'));
        self::assertEquals($b, \file_get_contents(__DIR__ . '/test b.md'));
        self::assertEquals($c, \file_get_contents(__DIR__ . '/test/test c.txt'));
        self::assertEquals($d, \file_get_contents(__DIR__ . '/test/test d.txt'));
        self::assertEquals($e, \file_get_contents(__DIR__ . '/test/sub/test e.txt'));

        \unlink(__DIR__ . '/test.zip');
        self::assertFileNotExists(__DIR__ . '/test.zip');
        self::assertFalse(Zip::unpack(__DIR__ . '/test.zip', __DIR__));
    }
}
