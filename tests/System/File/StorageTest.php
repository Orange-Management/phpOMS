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

namespace phpOMS\tests\System\File;

use phpOMS\System\File\Local\LocalStorage;
use phpOMS\System\File\Storage;

/**
 * @testdox phpOMS\tests\System\File\StorageTest: Storage handler for the different storage handler types
 *
 * @internal
 */
class StorageTest extends \PHPUnit\Framework\TestCase
{
    /**
     * @testdox By default the local storage handler is returned
     * @covers phpOMS\System\File\Storage
     */
    public function testStorageDefault() : void
    {
        self::assertInstanceOf('\phpOMS\System\File\Local\LocalStorage', Storage::env());
    }

    /**
     * @testdox The pre-defined storage handlers can be returned by their name
     * @covers phpOMS\System\File\Storage
     */
    public function testStoragePreDefined() : void
    {
        self::assertInstanceOf('\phpOMS\System\File\Local\LocalStorage', Storage::env('local'));
        self::assertInstanceOf('\phpOMS\System\File\Ftp\FtpStorage', Storage::env('ftp'));
    }

    /**
     * @testdox Storages can be registered and returned
     * @covers phpOMS\System\File\Storage
     */
    public function testInputOutput() : void
    {
        self::assertTrue(Storage::register('ftps', '\phpOMS\System\File\Ftp\FtpStorage'));
        self::assertTrue(Storage::register('test', LocalStorage::getInstance()));
        self::assertInstanceOf('\phpOMS\System\File\Ftp\FtpStorage', Storage::env('ftps'));
        self::assertInstanceOf('\phpOMS\System\File\Local\LocalStorage', Storage::env('test'));
    }

    /**
     * @testdox Registered storage handlers cannot be overwritten
     * @covers phpOMS\System\File\Storage
     */
    public function testInvalidRegister() : void
    {
        self::assertTrue(Storage::register('test2', LocalStorage::getInstance()));
        self::assertFalse(Storage::register('test2', LocalStorage::getInstance()));
    }

    /**
     * @testdox A invalid or none-existing storage throws a Exception
     * @covers phpOMS\System\File\Storage
     */
    public function testInvalidStorage() : void
    {
        self::expectException(\Exception::class);

        self::assertInstanceOf('\phpOMS\System\File\Local\LocalStorage', Storage::env('invalid'));
    }
}
