<?php
/**
 * Orange Management
 *
 * PHP Version 8.0
 *
 * @package   tests
 * @copyright Dennis Eichhorn
 * @license   OMS License 1.0
 * @version   1.0.0
 * @link      https://orange-management.org
 */
declare(strict_types=1);

namespace phpOMS\tests\Validation\Network;

use phpOMS\Validation\Network\Hostname;

/**
 * @testdox phpOMS\tests\Validation\Network\HostnameTest: Hostname validator
 *
 * @internal
 */
class HostnameTest extends \PHPUnit\Framework\TestCase
{
    /**
     * @testdox A hostname can be validated
     * @covers phpOMS\Validation\Network\Hostname
     * @group framework
     */
    public function testHostname() : void
    {
        self::assertTrue(Hostname::isValid('test.com'));
        self::assertFalse(Hostname::isValid('http://test.com'));
        self::assertFalse(Hostname::isValid('test.com/test?something=a'));
        self::assertFalse(Hostname::isValid('//somethign/wrong'));
    }
}
