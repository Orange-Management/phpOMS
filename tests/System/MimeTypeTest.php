<?php
/**
 * Karaka
 *
 * PHP Version 8.1
 *
 * @package   tests
 * @copyright Dennis Eichhorn
 * @license   OMS License 1.0
 * @version   1.0.0
 * @link      https://karaka.app
 */
declare(strict_types=1);

namespace phpOMS\tests\System;

require_once __DIR__ . '/../Autoloader.php';

use phpOMS\System\MimeType;

/**
 * @internal
 */
final class MimeTypeTest extends \PHPUnit\Framework\TestCase
{
    /**
     * @group framework
     * @coversNothing
     */
    public function testEnums() : void
    {
        $enums = MimeType::getConstants();

        foreach ($enums as $key => $value) {
            if (\stripos($value, '/') === false) {
                self::assertFalse(true);
            }
        }

        self::assertTrue(true);
    }

    /**
     * @covers phpOMS\System\MimeType
     * @group framework
     */
    public function testExtensionToMime() : void
    {
        self::assertEquals('application/pdf', MimeType::extensionToMime('pdf'));
    }

    /**
     * @covers phpOMS\System\MimeType
     * @group framework
     */
    public function testInvalidExtensionToMime() : void
    {
        self::assertEquals('application/octet-stream', MimeType::extensionToMime('INVALID'));
    }
}
