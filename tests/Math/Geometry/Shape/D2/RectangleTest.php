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

namespace phpOMS\tests\Math\Geometry\Shape\D2;

use phpOMS\Math\Geometry\Shape\D2\Rectangle;

class RectangleTest extends \PHPUnit\Framework\TestCase
{
    public function testRectanle() : void
    {
        self::assertEquals(10, Rectangle::getSurface(5, 2), '', 0.001);
        self::assertEquals(10, Rectangle::getPerimeter(2, 3), '', 0.001);
        self::assertEquals(32.7, Rectangle::getDiagonal(30, 13), '', 0.01);
    }
}
