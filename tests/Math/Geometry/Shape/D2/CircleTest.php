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

namespace phpOMS\tests\Math\Geometry\Shape\D2;

use phpOMS\Math\Geometry\Shape\D2\Circle;

/**
 * @internal
 */
class CircleTest extends \PHPUnit\Framework\TestCase
{
    public function testCircle() : void
    {
        self::assertEqualsWithDelta(12.57, Circle::getSurface(2), 0.01);
        self::assertEqualsWithDelta(12.57, Circle::getPerimeter(2), 0.01);
        self::assertEqualsWithDelta(2.0, Circle::getRadiusBySurface(Circle::getSurface(2)), 0.001);
        self::assertEqualsWithDelta(2.0, Circle::getRadiusByPerimeter(Circle::getPerimeter(2)), 0.001);
    }
}
