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

namespace phpOMS\tests\Localization;

use phpOMS\Localization\ISO3166TwoEnum;
use phpOMS\Localization\ISO4217CharEnum;
use phpOMS\Localization\ISO4217Enum;
use phpOMS\Localization\ISO639x1Enum;
use phpOMS\Localization\L11nManager;
use phpOMS\Localization\Localization;
use phpOMS\Localization\TimeZoneEnumArray;
use phpOMS\Utils\Converter\AngleType;
use phpOMS\Utils\Converter\TemperatureType;

require_once __DIR__ . '/../Autoloader.php';

/**
 * @internal
 */
class LocalizationTest extends \PHPUnit\Framework\TestCase
{
    protected $l11nManager = null;

    protected function setUp() : void
    {
        $this->l11nManager = new L11nManager('Api');
    }

    public function testAttributes() : void
    {
        $localization = new Localization();
        self::assertObjectHasAttribute('country', $localization);
        self::assertObjectHasAttribute('timezone', $localization);
        self::assertObjectHasAttribute('language', $localization);
        self::assertObjectHasAttribute('currency', $localization);
        self::assertObjectHasAttribute('decimal', $localization);
        self::assertObjectHasAttribute('thousands', $localization);
        self::assertObjectHasAttribute('datetime', $localization);
    }

    public function testDefault() : void
    {
        $localization = new Localization();
        self::assertTrue(ISO3166TwoEnum::isValidValue($localization->getCountry()));
        self::assertTrue(TimeZoneEnumArray::isValidValue($localization->getTimezone()));
        self::assertTrue(ISO639x1Enum::isValidValue($localization->getLanguage()));
        self::assertTrue(ISO4217Enum::isValidValue($localization->getCurrency()));
        self::assertEquals('.', $localization->getDecimal());
        self::assertEquals(',', $localization->getThousands());
        self::assertEquals([], $localization->getDatetime());

        self::assertEquals([], $localization->getSpeed());
        self::assertEquals([], $localization->getWeight());
        self::assertEquals([], $localization->getLength());
        self::assertEquals([], $localization->getArea());
        self::assertEquals([], $localization->getVolume());
    }

    public function testInvalidLanguage() : void
    {
        self::expectException(\phpOMS\Stdlib\Base\Exception\InvalidEnumValue::class);

        $localization = new Localization();
        $localization->setLanguage('abc');
    }

    public function testInvalidCountry() : void
    {
        self::expectException(\phpOMS\Stdlib\Base\Exception\InvalidEnumValue::class);

        $localization = new Localization();
        $localization->setCountry('abc');
    }

    public function testInvalidTimezone() : void
    {
        self::expectException(\phpOMS\Stdlib\Base\Exception\InvalidEnumValue::class);

        $localization = new Localization();
        $localization->setTimezone('abc');
    }

    public function testInvalidCurrency() : void
    {
        self::expectException(\phpOMS\Stdlib\Base\Exception\InvalidEnumValue::class);

        $localization = new Localization();
        $localization->setCurrency('abc');
    }

    public function testGetSet() : void
    {
        $localization = new Localization();

        $localization->setCountry(ISO3166TwoEnum::_USA);
        self::assertEquals(ISO3166TwoEnum::_USA, $localization->getCountry());

        $localization->setTimezone(TimeZoneEnumArray::get(315));
        self::assertEquals(TimeZoneEnumArray::get(315), $localization->getTimezone());

        $localization->setLanguage(ISO639x1Enum::_DE);
        self::assertEquals(ISO639x1Enum::_DE, $localization->getLanguage());

        $localization->setCurrency(ISO4217CharEnum::_EUR);
        self::assertEquals(ISO4217CharEnum::_EUR, $localization->getCurrency());

        $localization->setDatetime(['Y-m-d H:i:s']);
        self::assertEquals(['Y-m-d H:i:s'], $localization->getDatetime());

        $localization->setDecimal(',');
        self::assertEquals(',', $localization->getDecimal());

        $localization->setThousands('.');
        self::assertEquals('.', $localization->getThousands());

        $localization->setAngle(AngleType::CENTRAD);
        self::assertEquals(AngleType::CENTRAD, $localization->getAngle());

        $localization->setTemperature(TemperatureType::FAHRENHEIT);
        self::assertEquals(TemperatureType::FAHRENHEIT, $localization->getTemperature());

        $localization->setWeight([1]);
        $localization->setLength([1]);
        $localization->setArea([1]);
        $localization->setVolume([1]);
        $localization->setSpeed([1]);
        self::assertEquals([1], $localization->getWeight());
        self::assertEquals([1], $localization->getLength());
        self::assertEquals([1], $localization->getArea());
        self::assertEquals([1], $localization->getVolume());
        self::assertEquals([1], $localization->getSpeed());
    }

    public function testLocalizationLoading() : void
    {
        $localization = new Localization();
        $localization->loadFromLanguage(ISO639x1Enum::_EN);
        self::assertEquals(ISO4217CharEnum::_USD, $localization->getCurrency());

        $localization->loadFromLanguage(ISO639x1Enum::_AA);
        self::assertEquals(ISO4217CharEnum::_USD, $localization->getCurrency());

        $localization->loadFromLanguage(ISO639x1Enum::_AA, 'ABC');
        self::assertEquals(ISO4217CharEnum::_USD, $localization->getCurrency());
    }

    public function testInvalidLocalizationLoading() : void
    {
        self::expectException(\phpOMS\Stdlib\Base\Exception\InvalidEnumValue::class);

        $localization = new Localization();
        $localization->loadFromLanguage('INVALID');
    }
}
