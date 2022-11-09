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

namespace phpOMS\tests\Validation\Finance;

use phpOMS\Validation\Finance\BIC;

/**
 * @testdox phpOMS\tests\Validation\Finance\BICTest: BIC validator
 *
 * @internal
 */
final class BICTest extends \PHPUnit\Framework\TestCase
{
    /**
     * @testdox A BIC can be validated
     * @covers phpOMS\Validation\Finance\BIC
     * @group framework
     */
    public function testBic() : void
    {
        self::assertTrue(BIC::isValid('ASPKAT2LXXX'));
        self::assertTrue(BIC::isValid('ASPKAT2L'));
        self::assertTrue(BIC::isValid('DSBACNBXSHA'));
        self::assertTrue(BIC::isValid('UNCRIT2B912'));
        self::assertTrue(BIC::isValid('DABADKKK'));
        self::assertTrue(BIC::isValid('RZOOAT2L303'));

        self::assertFalse(BIC::isValid('ASPKAT2LXX'));
        self::assertFalse(BIC::isValid('ASPKAT2LX'));
        self::assertFalse(BIC::isValid('ASPKAT2LXXX1'));
        self::assertFalse(BIC::isValid('DABADKK'));
        self::assertFalse(BIC::isValid('RZ00AT2L303'));
        self::assertFalse(BIC::isValid('1SBACNBXSHA'));
        self::assertFalse(BIC::isValid('D2BACNBXSHA'));
        self::assertFalse(BIC::isValid('DS3ACNBXSHA'));
        self::assertFalse(BIC::isValid('DSB4CNBXSHA'));
        self::assertFalse(BIC::isValid('DSBA5NBXSHA'));
        self::assertFalse(BIC::isValid('DSBAC6BXSHA'));
        self::assertFalse(BIC::isValid('1S3AC6BXSHA'));
    }
}
