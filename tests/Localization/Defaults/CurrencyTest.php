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

namespace phpOMS\tests\Localization\Defaults;

require_once __DIR__ . '/../../Autoloader.php';

use phpOMS\Localization\Defaults\Currency;

/**
 * @testdox phpOMS\tests\Localization\Defaults\CurrencyTest: Currency database model
 *
 * @internal
 */
class CurrencyTest extends \PHPUnit\Framework\TestCase
{
    /**
     * @testdox The model has the expected member variables and default values
     * @covers phpOMS\Localization\Defaults\Currency
     * @group framework
     */
    public function testDefaults() : void
    {
        $obj = new Currency();
        self::assertEquals('', $obj->getName());
        self::assertEquals('', $obj->getNumber());
        self::assertEquals('', $obj->getSymbol());
        self::assertEquals(0, $obj->getSubunits());
        self::assertEquals('', $obj->getDecimals());
        self::assertEquals('', $obj->getCountries());
        self::assertEquals('', $obj->getCode());
    }
}
