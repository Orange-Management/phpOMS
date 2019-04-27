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
 declare(strict_types=1);

namespace phpOMS\tests\System\File;

use phpOMS\System\File\PathException;

/**
 * @internal
 */
class PathExceptionTest extends \PHPUnit\Framework\TestCase
{
    public function testConstructor() : void
    {
        $e = new PathException('test.file');
        self::assertStringContainsString('test.file', $e->getMessage());
        self::assertEquals(0, $e->getCode());
        $this->isInstanceOf('\UnexpectedValueException');
    }
}
