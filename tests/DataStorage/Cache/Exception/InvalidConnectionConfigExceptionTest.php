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

namespace phpOMS\tests\DataStorage\Cache\Exception;

use phpOMS\DataStorage\Cache\Exception\InvalidConnectionConfigException;

class InvalidConnectionConfigExceptionTest extends \PHPUnit\Framework\TestCase
{
    public function testException()
    {
        self::assertInstanceOf(\InvalidArgumentException::class, new InvalidConnectionConfigException(''));
    }
}
