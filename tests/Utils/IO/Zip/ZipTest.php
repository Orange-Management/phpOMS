<?php
/**
 * Jingga
 *
 * PHP Version 8.2
 *
 * @package   tests
 * @copyright Dennis Eichhorn
 * @license   OMS License 2.0
 * @version   1.0.0
 * @link      https://jingga.app
 */
declare(strict_types=1);

namespace phpOMS\tests\Utils\IO\Zip;

use phpOMS\Utils\IO\Zip\Zip;

/**
 * @internal
 */
#[\PHPUnit\Framework\Attributes\CoversClass(\phpOMS\Utils\IO\Zip\Zip::class)]
#[\PHPUnit\Framework\Attributes\TestDox('phpOMS\tests\Utils\IO\Zip\ZipTest: Zip archive')]
final class ZipTest extends \PHPUnit\Framework\TestCase
{
    protected function setUp() : void
    {
        if (!\extension_loaded('zip')) {
            $this->markTestSkipped(
              'The ZIP extension is not available.'
            );
        }

        if (\is_dir('new_dir')) {
            \rmdir('new_dir');
        }
    }

    #[\PHPUnit\Framework\Attributes\Group('framework')]
    #[\PHPUnit\Framework\Attributes\TestDox('Data can be zip packed and unpacked')]
    public function testZip() : void
    {
        self::assertTrue(Zip::pack(
            [
                __DIR__ . '/test a.txt' => 'test a.txt',
                __DIR__ . '/test b.md'  => 'test b.md',
                __DIR__ . '/invalid.so' => 'invalid.so',
                __DIR__ . '/test'       => 'test',
                __DIR__ . '/invalid.txt',
            ],
            __DIR__ . '/test.zip'
        ));

        self::assertFileExists(__DIR__ . '/test.zip');

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

        // second test
        self::assertTrue(Zip::pack(
            __DIR__ . '/test',
            __DIR__ . '/test.zip'
        ));

        self::assertTrue(Zip::unpack(__DIR__ . '/test.zip', __DIR__ . '/new_dir'));
        self::assertFileExists(__DIR__ . '/new_dir');
        self::assertEquals($c, \file_get_contents(__DIR__ . '/new_dir/test c.txt'));

        \unlink(__DIR__ . '/new_dir/test c.txt');
        \unlink(__DIR__ . '/new_dir/test d.txt');
        \unlink(__DIR__ . '/new_dir/sub/test e.txt');
        \rmdir(__DIR__ . '/new_dir/sub');
        \rmdir(__DIR__ . '/new_dir');

        \unlink(__DIR__ . '/test.zip');
    }

    #[\PHPUnit\Framework\Attributes\Group('framework')]
    #[\PHPUnit\Framework\Attributes\TestDox('The output of the zip archive needs to be properly defined')]
    public function testInvalidZipDestination() : void
    {
        self::assertFalse(Zip::pack(
            [
                __DIR__ . '/test a.txt' => 'test a.txt',
                __DIR__ . '/test b.md'  => 'test b.md',
                __DIR__ . '/test'       => 'test',
            ],
            __DIR__
        ));
    }

    #[\PHPUnit\Framework\Attributes\Group('framework')]
    #[\PHPUnit\Framework\Attributes\TestDox('Extracting invalid zip files fail')]
    public function testInvalidZipUnpack() : void
    {
        self::assertFalse(Zip::unpack(
            __DIR__ . '/invalid.zip',
            __DIR__
        ));

        self::assertFalse(Zip::unpack(
            __DIR__ . '/test a.txt',
            __DIR__
        ));
    }

    #[\PHPUnit\Framework\Attributes\Group('framework')]
    #[\PHPUnit\Framework\Attributes\TestDox('A zip archive cannot be overwritten by default')]
    public function testInvalidZip() : void
    {
        Zip::pack(
            [
                __DIR__ . '/test a.txt' => 'test a.txt',
                __DIR__ . '/test b.md'  => 'test b.md',
                __DIR__ . '/test'       => 'test',
            ],
            __DIR__ . '/test2.zip'
        );

        self::assertFalse(Zip::pack(
            [
                __DIR__ . '/test a.txt' => 'test a.txt',
                __DIR__ . '/test b.md'  => 'test b.md',
                __DIR__ . '/test'       => 'test',
            ],
            __DIR__ . '/test2.zip'
        ));

        \unlink(__DIR__ . '/test2.zip');
    }

    public function testInvalidArchiveUnpack() : void
    {
        self::assertFalse(Zip::unpack(__DIR__ . '/malformed.zip', __DIR__));
    }

    #[\PHPUnit\Framework\Attributes\Group('framework')]
    #[\PHPUnit\Framework\Attributes\TestDox('A none-existing source cannot be unpacked')]
    public function testInvalidUnpackSource() : void
    {
        self::assertFalse(Zip::unpack(__DIR__ . '/test.zip', __DIR__));
    }

    #[\PHPUnit\Framework\Attributes\Group('framework')]
    #[\PHPUnit\Framework\Attributes\TestDox('A destination cannot be overwritten')]
    public function testInvalidUnpackDestination() : void
    {
        self::assertTrue(Zip::pack(
            [
                __DIR__ . '/test a.txt' => 'test a.txt',
                __DIR__ . '/test b.md'  => 'test b.md',
                __DIR__ . '/test'       => 'test',
            ],
            __DIR__ . '/test3.zip'
        ));

        Zip::unpack(__DIR__ . '/abc/test3.zip', __DIR__);
        self::assertFalse(Zip::unpack(__DIR__ . '/abc/test3.zip', __DIR__));

        \unlink(__DIR__ . '/test3.zip');
    }
}
