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

namespace phpOMS\tests\System\File\Ftp;

use phpOMS\System\File\ContentPutMode;
use phpOMS\System\File\Ftp\Directory;
use phpOMS\System\File\Ftp\File;
use phpOMS\Uri\HttpUri;

/**
 * @testdox phpOMS\tests\System\File\Ftp\FileTest: File handler for a ftp server
 *
 * @internal
 */
class FileTest extends \PHPUnit\Framework\TestCase
{
    const BASE = 'ftp://test:123456@127.0.0.1:20';

    private $con = null;

    protected function setUp() : void
    {
        if ($this->con === null) {
            $this->con = File::ftpConnect(new HttpUri(self::BASE));
        }

        if ($this->con === false) {
            $this->markTestSkipped(
              'The ftp connection is not available.'
            );
        }
    }

    /**
     * @testdox A file without content can be created
     * @covers phpOMS\System\File\Ftp\File
     * @group framework
     */
    public function testStaticCreate() : void
    {
        $testFile = __DIR__ . '/test.txt';
        self::assertTrue(File::create($this->con, $testFile));
        self::assertTrue(\is_file($testFile));
        self::assertEquals('', \file_get_contents($testFile));

        File::delete($this->con, $testFile);
    }

    /**
     * @testdox A file cannot be created if it already exists
     * @covers phpOMS\System\File\Ftp\File
     * @group framework
     */
    public function testInvalidStaticCreate() : void
    {
        $testFile = __DIR__ . '/test.txt';
        self::assertTrue(File::create($this->con, $testFile));
        self::assertFalse(File::create($this->con, $testFile));
        self::assertTrue(\is_file($testFile));

        File::delete($this->con, $testFile);
    }

    /**
     * @testdox A file with content can be created
     * @covers phpOMS\System\File\Ftp\File
     * @group framework
     */
    public function testStaticPut() : void
    {
        $testFile = __DIR__ . '/test.txt';
        self::assertTrue(File::put($this->con, $testFile, 'test', ContentPutMode::CREATE));
        self::assertTrue(\is_file($testFile));
        self::assertEquals('test', \file_get_contents($testFile));

        File::delete($this->con, $testFile);
    }

    /**
     * @testdox A file cannot be replaced if it doesn't exists
     * @covers phpOMS\System\File\Ftp\File
     * @group framework
     */
    public function testInvalidStaticCreateReplace() : void
    {
        $testFile = __DIR__ . '/test.txt';
        self::assertFalse(File::put($this->con, $testFile, 'test', ContentPutMode::REPLACE));
        self::assertfalse(\file_exists($testFile));
    }

    /**
     * @testdox A file cannot be appended if it doesn't exists
     * @covers phpOMS\System\File\Ftp\File
     * @group framework
     */
    public function testInvalidStaticCreateAppend() : void
    {
        $testFile = __DIR__ . '/test.txt';
        self::assertFalse(File::put($this->con, $testFile, 'test', ContentPutMode::APPEND));
        self::assertfalse(\file_exists($testFile));
    }

    /**
     * @testdox A file cannot be prepended if it doesn't exists
     * @covers phpOMS\System\File\Ftp\File
     * @group framework
     */
    public function testInvalidStaticCreatePrepend() : void
    {
        $testFile = __DIR__ . '/test.txt';
        self::assertFalse(File::put($this->con, $testFile, 'test', ContentPutMode::PREPEND));
        self::assertfalse(\file_exists($testFile));
    }

    /**
     * @testdox A file can be checked for existence
     * @covers phpOMS\System\File\Ftp\File
     * @group framework
     */
    public function testStaticExists() : void
    {
        self::assertTrue(File::exists($this->con, __DIR__ . '/FileTest.php'));
        self::assertFalse(File::exists($this->con, __DIR__ . '/invalid/file.txt'));
    }

    /**
     * @testdox A file can be replaced with a new one
     * @covers phpOMS\System\File\Ftp\File
     * @group framework
     */
    public function testStaticReplace() : void
    {
        $testFile = __DIR__ . '/test.txt';
        self::assertTrue(File::put($this->con, $testFile, 'test', ContentPutMode::CREATE));
        self::assertTrue(File::put($this->con, $testFile, 'test2', ContentPutMode::REPLACE));

        self::assertEquals('test2', \file_get_contents($testFile));

        File::delete($this->con, $testFile);
    }

    /**
     * @testdox The set alias works like the replace flag
     * @covers phpOMS\System\File\Ftp\File
     * @group framework
     */
    public function testStaticSetAlias() : void
    {
        $testFile = __DIR__ . '/test.txt';
        self::assertTrue(File::put($this->con, $testFile, 'test', ContentPutMode::CREATE));
        self::assertTrue(File::set($this->con, $testFile, 'test2'));

        self::assertEquals('test2', \file_get_contents($testFile));

        File::delete($this->con, $testFile);
    }

    /**
     * @testdox A file can be appended with additional content
     * @covers phpOMS\System\File\Ftp\File
     * @group framework
     */
    public function testStaticAppend() : void
    {
        $testFile = __DIR__ . '/test.txt';
        self::assertTrue(File::put($this->con, $testFile, 'test', ContentPutMode::CREATE));
        self::assertTrue(File::put($this->con, $testFile, 'test2', ContentPutMode::APPEND));

        self::assertEquals('testtest2', \file_get_contents($testFile));

        File::delete($this->con, $testFile);
    }

    /**
     * @testdox The append alias works like the append flag
     * @covers phpOMS\System\File\Ftp\File
     * @group framework
     */
    public function testStaticAppendAlias() : void
    {
        $testFile = __DIR__ . '/test.txt';
        self::assertTrue(File::put($this->con, $testFile, 'test', ContentPutMode::CREATE));
        self::assertTrue(File::append($this->con, $testFile, 'test2'));

        self::assertEquals('testtest2', \file_get_contents($testFile));

        File::delete($this->con, $testFile);
    }

    /**
     * @testdox A file can be prepended with additional content
     * @covers phpOMS\System\File\Ftp\File
     * @group framework
     */
    public function testStaticPrepend() : void
    {
        $testFile = __DIR__ . '/test.txt';
        self::assertTrue(File::put($this->con, $testFile, 'test', ContentPutMode::CREATE));
        self::assertTrue(File::put($this->con, $testFile, 'test2', ContentPutMode::PREPEND));

        self::assertEquals('test2test', \file_get_contents($testFile));

        File::delete($this->con, $testFile);
    }

    /**
     * @testdox The prepend alias works like the prepend flag
     * @covers phpOMS\System\File\Ftp\File
     * @group framework
     */
    public function testStaticPrependAlias() : void
    {
        $testFile = __DIR__ . '/test.txt';
        self::assertTrue(File::put($this->con, $testFile, 'test', ContentPutMode::CREATE));
        self::assertTrue(File::prepend($this->con, $testFile, 'test2'));

        self::assertEquals('test2test', \file_get_contents($testFile));

        File::delete($this->con, $testFile);
    }

    /**
     * @testdox The content of a file can be read
     * @covers phpOMS\System\File\Ftp\File
     * @group framework
     */
    public function testStaticGet() : void
    {
        $testFile = __DIR__ . '/test.txt';
        self::assertTrue(File::put($this->con, $testFile, 'test', ContentPutMode::CREATE));
        self::assertEquals('test', File::get($this->con, $testFile));

        File::delete($this->con, $testFile);
    }

    /**
     * @testdox The parent directory of a file can be returned
     * @covers phpOMS\System\File\Ftp\File
     * @group framework
     */
    public function testStaticParent() : void
    {
        $testFile = __DIR__ . '/test.txt';

        self::assertEquals(\str_replace('\\', '/', \realpath(__DIR__ . '/../')), File::parent($testFile));
    }

    /**
     * @testdox The extension of a file can be returned
     * @covers phpOMS\System\File\Ftp\File
     * @group framework
     */
    public function testStaticExtension() : void
    {
        $testFile = __DIR__ . '/test.txt';

        self::assertEquals('txt', File::extension($testFile));
    }

    /**
     * @testdox The name of a file can be returned
     * @covers phpOMS\System\File\Ftp\File
     * @group framework
     */
    public function testStaticName() : void
    {
        $testFile = __DIR__ . '/test.txt';

        self::assertEquals('test', File::name($testFile));
    }

    /**
     * @testdox The basename of a file can be returned
     * @covers phpOMS\System\File\Ftp\File
     * @group framework
     */
    public function testStaticBaseName() : void
    {
        $testFile = __DIR__ . '/test.txt';

        self::assertEquals('test.txt', File::basename($testFile));
    }

    /**
     * @testdox The file name of a file can be returned
     * @covers phpOMS\System\File\Ftp\File
     * @group framework
     */
    public function testStaticDirname() : void
    {
        $testFile = __DIR__ . '/test.txt';

        self::assertEquals(\basename(\realpath(__DIR__)), File::dirname($testFile));
    }

    /**
     * @testdox The file path of a file can be returned
     * @covers phpOMS\System\File\Ftp\File
     * @group framework
     */
    public function testStaticDirectoryPath() : void
    {
        $testFile = __DIR__ . '/test.txt';

        self::assertEquals(\realpath(__DIR__), File::dirpath($testFile));
    }

    /**
     * @testdox The count of a file is always 1
     * @covers phpOMS\System\File\Ftp\File
     * @group framework
     */
    public function testStaticCount() : void
    {
        $testFile = __DIR__ . '/test.txt';

        self::assertEquals(1, File::count($testFile));
    }

    /**
     * @testdox The directories creation date can be returned
     * @covers phpOMS\System\File\Ftp\File
     * @group framework
     */
    public function testStaticCreatedAt() : void
    {
        $testFile = __DIR__ . '/test.txt';
        self::assertTrue(File::create($this->con, $testFile));

        $now = new \DateTime('now');
        self::assertEquals($now->format('Y-m-d'), File::created($this->con, $testFile)->format('Y-m-d'));

        File::delete($this->con, $testFile);
    }

    /**
     * @testdox The directories last change date can be returned
     * @covers phpOMS\System\File\Ftp\File
     * @group framework
     */
    public function testStaticChangedAt() : void
    {
        $testFile = __DIR__ . '/test.txt';
        self::assertTrue(File::create($this->con, $testFile));

        $now = new \DateTime('now');
        self::assertEquals($now->format('Y-m-d'), File::changed($this->con, $testFile)->format('Y-m-d'));

        File::delete($this->con, $testFile);
    }

    /**
     * @testdox A file can be deleted
     * @covers phpOMS\System\File\Ftp\File
     * @group framework
     */
    public function testStaticDelete() : void
    {
        $testFile = __DIR__ . '/test.txt';

        self::assertTrue(File::create($this->con, $testFile));
        self::assertTrue(File::delete($this->con, $testFile));
        self::assertFalse(File::exists($this->con, $testFile));
    }

    /**
     * @testdox A none-existing file cannot be deleted
     * @covers phpOMS\System\File\Ftp\File
     * @group framework
     */
    public function testInvalidStaticDelete() : void
    {
        $testFile = __DIR__ . '/test.txt';

        self::assertFalse(File::delete($this->con, $testFile));
    }

    /**
     * @testdox The size of a file can be returned
     * @covers phpOMS\System\File\Ftp\File
     * @group framework
     */
    public function testStaticSize() : void
    {
        $testFile = __DIR__ . '/test.txt';
        File::put($this->con, $testFile, 'test', ContentPutMode::CREATE);

        self::assertGreaterThan(0, File::size($this->con, $testFile));

        File::delete($this->con, $testFile);
    }

    /**
     * @testdox The permission of a file can be returned
     * @covers phpOMS\System\File\Ftp\File
     * @group framework
     */
    public function testStaticPermission() : void
    {
        $testFile = __DIR__ . '/test.txt';
        File::put($this->con, $testFile, 'test', ContentPutMode::CREATE);

        self::assertGreaterThan(0, File::permission($this->con, $testFile));

        File::delete($this->con, $testFile);
    }

    /**
     * @testdox The permission of a none-existing file is negative
     * @covers phpOMS\System\File\Ftp\File
     * @group framework
     */
    public function testInvalidStaticPermission() : void
    {
        $testFile = __DIR__ . '/test.txt';
        self::assertEquals(-1, File::permission($this->con, $testFile));
    }

    /**
     * @testdox A file can be copied to a different location
     * @covers phpOMS\System\File\Ftp\File
     * @group framework
     */
    public function testStaticCopy() : void
    {
        $testFile = __DIR__ . '/test.txt';
        $newPath  = __DIR__ . '/sub/path/testing.txt';

        File::put($this->con, $testFile, 'test', ContentPutMode::CREATE);

        self::assertTrue(File::copy($this->con, $testFile, $newPath));
        self::assertTrue(File::exists($this->con, $newPath));
        self::assertEquals('test', File::get($this->con, $newPath));

        File::delete($this->con, $newPath);
        Directory::delete($this->con, __DIR__ . '/sub/path/');
        Directory::delete($this->con, __DIR__ . '/sub/');

        File::delete($this->con, $testFile);
    }

    /**
     * @testdox A file cannot be copied to a different location if the destination already exists
     * @covers phpOMS\System\File\Ftp\File
     * @group framework
     */
    public function testInvalidStaticCopy() : void
    {
        $testFile = __DIR__ . '/test.txt';
        $newPath  = __DIR__ . '/test2.txt';

        File::put($this->con, $testFile, 'test', ContentPutMode::CREATE);
        File::put($this->con, $newPath, 'test2', ContentPutMode::CREATE);

        self::assertFalse(File::copy($this->con, $testFile, $newPath));
        self::assertEquals('test2', File::get($this->con, $newPath));

        File::delete($this->con, $newPath);
        File::delete($this->con, $testFile);
    }

    /**
     * @testdox A file can be forced to be copied to a different location even if the destination already exists
     * @covers phpOMS\System\File\Ftp\File
     * @group framework
     */
    public function testStaticCopyOverwrite() : void
    {
        $testFile = __DIR__ . '/test.txt';
        $newPath  = __DIR__ . '/test2.txt';

        File::put($this->con, $testFile, 'test', ContentPutMode::CREATE);
        File::put($this->con, $newPath, 'test2', ContentPutMode::CREATE);

        self::assertTrue(File::copy($this->con, $testFile, $newPath, true));
        self::assertEquals('test', File::get($this->con, $newPath));

        File::delete($this->con, $newPath);
        File::delete($this->con, $testFile);
    }

    /**
     * @testdox A file can be moved to a different location
     * @covers phpOMS\System\File\Ftp\File
     * @group framework
     */
    public function testStaticMove() : void
    {
        $testFile = __DIR__ . '/test.txt';
        $newPath  = __DIR__ . '/sub/path/testing.txt';

        File::put($this->con, $testFile, 'test', ContentPutMode::CREATE);

        self::assertTrue(File::move($this->con, $testFile, $newPath));
        self::assertFalse(File::exists($this->con, $testFile));
        self::assertTrue(File::exists($this->con, $newPath));
        self::assertEquals('test', File::get($this->con, $newPath));

        Directory::delete($this->con, __DIR__ . '/sub');
    }

    /**
     * @testdox A file cannot be moved to a different location if the destination already exists
     * @covers phpOMS\System\File\Ftp\File
     * @group framework
     */
    public function testInvalidStaticMove() : void
    {
        $testFile = __DIR__ . '/test.txt';
        $newPath  = __DIR__ . '/test2.txt';

        File::put($this->con, $testFile, 'test', ContentPutMode::CREATE);
        File::put($this->con, $newPath, 'test2', ContentPutMode::CREATE);

        self::assertFalse(File::move($this->con, $testFile, $newPath));
        self::assertTrue(File::exists($this->con, $testFile));
        self::assertEquals('test2', File::get($this->con, $newPath));

        File::delete($this->con, $newPath);
        File::delete($this->con, $testFile);
    }

    /**
     * @testdox A file can be forced to be moved to a different location even if the destination already exists
     * @covers phpOMS\System\File\Ftp\File
     * @group framework
     */
    public function testStaticMoveOverwrite() : void
    {
        $testFile = __DIR__ . '/test.txt';
        $newPath  = __DIR__ . '/test2.txt';

        File::put($this->con, $testFile, 'test', ContentPutMode::CREATE);
        File::put($this->con, $newPath, 'test2', ContentPutMode::CREATE);

        self::assertTrue(File::move($this->con, $testFile, $newPath, true));
        self::assertFalse(File::exists($this->con, $testFile));
        self::assertEquals('test', File::get($this->con, $newPath));

        File::delete($this->con, $newPath);
    }

    /**
     * @testdox The size of a none-existing file is negative
     * @covers phpOMS\System\File\Ftp\File
     * @group framework
     */
    public function testInvalidSizePath() : void
    {
        self::assertEquals(-1, File::size($this->con, __DIR__ . '/invalid.txt'));
    }

    /**
     * @testdox A none-existing file cannot be copied
     * @covers phpOMS\System\File\Ftp\File
     * @group framework
     */
    public function testInvalidCopyPath() : void
    {
        self::assertFalse(File::copy($this->con, __DIR__ . '/invalid.txt', __DIR__ . '/invalid2.txt'));
    }

    /**
     * @testdox A none-existing file cannot be moved
     * @covers phpOMS\System\File\Ftp\File
     * @group framework
     */
    public function testInvalidMovePath() : void
    {
        self::assertFalse(File::move($this->con, __DIR__ . '/invalid.txt', __DIR__ . '/invalid2.txt'));
    }

    /**
     * @testdox Reading the content of a none-existing file throws a PathException
     * @covers phpOMS\System\File\Ftp\File
     * @group framework
     */
    public function testInvalidGetPath() : void
    {
        $this->expectException(\phpOMS\System\File\PathException::class);

        File::get($this->con, __DIR__ . '/invalid.txt');
    }

    /**
     * @testdox Reading the created date of a none-existing file throws a PathException
     * @covers phpOMS\System\File\Ftp\File
     * @group framework
     */
    public function testInvalidCreatedPath() : void
    {
        $this->expectException(\phpOMS\System\File\PathException::class);

        File::created($this->con, __DIR__ . '/invalid.txt');
    }

    /**
     * @testdox Reading the last change date of a none-existing file throws a PathException
     * @covers phpOMS\System\File\Ftp\File
     * @group framework
     */
    public function testInvalidChangedPath() : void
    {
        $this->expectException(\phpOMS\System\File\PathException::class);

        File::changed($this->con, __DIR__ . '/invalid.txt');
    }

    /**
     * @testdox Reading the owner of a none-existing file throws a PathException
     * @covers phpOMS\System\File\Ftp\File
     * @group framework
     */
    public function testInvalidOwnerPath() : void
    {
        $this->expectException(\phpOMS\System\File\PathException::class);

        File::owner($this->con, __DIR__ . '/invalid.txt');
    }
}
