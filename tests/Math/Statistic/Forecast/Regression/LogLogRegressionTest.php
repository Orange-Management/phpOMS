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

namespace phpOMS\tests\Math\Statistic\Forecast\Regression;

use phpOMS\Math\Statistic\Forecast\Regression\LogLogRegression;

class LogLogRegressionTest extends \PHPUnit\Framework\TestCase
{
    protected $reg = null;

    protected function setUp() : void
    {
        // ln(y) = 2 + 3 * ln(x) => y = e^(2 + 3 * ln(x))
        $x = [0.25, 0.5, 1, 1.5];
        $y = [0.115, 0.924, 7.389, 24.938];

        $this->reg = LogLogRegression::getRegression($x, $y);
    }

    public function testRegression() : void
    {
        self::assertEquals(['b0' => 2, 'b1' => 3], $this->reg, '', 0.2);
    }

    public function testSlope() : void
    {
        $y = 3;
        $x = 2;
        self::assertEquals($this->reg['b1'] * $y / $x, LogLogRegression::getSlope($this->reg['b1'], $y, $x), '', 0.2);
    }

    public function testElasticity() : void
    {
        self::assertEquals($this->reg['b1'], LogLogRegression::getElasticity($this->reg['b1'], 0, 0), '', 0.2);
    }

    /**
     * @expectedException \phpOMS\Math\Matrix\Exception\InvalidDimensionException
     */
    public function testInvalidDimension() : void
    {
        $x = [1,2, 3];
        $y = [1,2, 3, 4];

        LogLogRegression::getRegression($x, $y);
    }
}
