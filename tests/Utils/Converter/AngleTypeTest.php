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

namespace phpOMS\tests\Utils\Converter;

use phpOMS\Utils\Converter\AngleType;

/**
 * @internal
 */
class AngleTypeTest extends \PHPUnit\Framework\TestCase
{
    public function testEnums() : void
    {
        self::assertCount(10, AngleType::getConstants());
        self::assertEquals(AngleType::getConstants(), \array_unique(AngleType::getConstants()));

        self::assertEquals('deg', AngleType::DEGREE);
        self::assertEquals('rad', AngleType::RADIAN);
        self::assertEquals('arcsec', AngleType::SECOND);
        self::assertEquals('arcmin', AngleType::MINUTE);
        self::assertEquals('mil (us ww2)', AngleType::MILLIRADIAN_US);
        self::assertEquals('mil (uk)', AngleType::MILLIRADIAN_UK);
        self::assertEquals('mil (ussr)', AngleType::MILLIRADIAN_USSR);
        self::assertEquals('mil (nato)', AngleType::MILLIRADIAN_NATO);
        self::assertEquals('g', AngleType::GRADIAN);
        self::assertEquals('crad', AngleType::CENTRAD);
    }
}
