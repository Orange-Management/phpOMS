<?php
/**
 * Jingga
 *
 * PHP Version 8.1
 *
 * @package   tests
 * @copyright Dennis Eichhorn
 * @license   OMS License 2.0
 * @version   1.0.0
 * @link      https://jingga.app
 */
declare(strict_types=1);

namespace phpOMS\tests\Math\Topology;

use phpOMS\Math\Topology\Kernel2D;

/**
 * @testdox phpOMS\tests\Math\Topology\Kernel2DTest: Metric/distance calculations
 *
 * @internal
 */
final class Kernel2DTest extends \PHPUnit\Framework\TestCase
{
    public function testUniform() : void
    {
        self::assertEquals(0.5, Kernel2D::uniformKernel(1, 0));
        self::assertEquals(0.5, Kernel2D::uniformKernel(1, -1));
        self::assertEquals(0.5, Kernel2D::uniformKernel(1, 1));

        self::assertEquals(0.0, Kernel2D::uniformKernel(1, 2));
        self::assertEquals(0.0, Kernel2D::uniformKernel(1, -2));
    }

    public function testTriangle() : void
    {
        self::assertEquals(1.0, Kernel2D::triangularKernel(1, 0));
        self::assertEquals(0.0, Kernel2D::triangularKernel(1, -1));
        self::assertEquals(0.0, Kernel2D::triangularKernel(1, 1));

        self::assertEquals(0.0, Kernel2D::triangularKernel(1, 2));
        self::assertEquals(0.0, Kernel2D::triangularKernel(1, -2));
    }

    public function testEpanechnikov() : void
    {
        self::assertEquals(3 / 4, Kernel2D::epanechnikovKernel(1, 0));
        self::assertEquals(0.0, Kernel2D::epanechnikovKernel(1, -1));
        self::assertEquals(0.0, Kernel2D::epanechnikovKernel(1, 1));

        self::assertEquals(0.0, Kernel2D::epanechnikovKernel(1, 2));
        self::assertEquals(0.0, Kernel2D::epanechnikovKernel(1, -2));
    }

    public function testQuartic() : void
    {
        self::assertEquals(15 / 6, Kernel2D::quarticKernel(1, 0));
        self::assertEquals(0.0, Kernel2D::quarticKernel(1, -1));
        self::assertEquals(0.0, Kernel2D::quarticKernel(1, 1));

        self::assertEquals(0.0, Kernel2D::quarticKernel(1, 2));
        self::assertEquals(0.0, Kernel2D::quarticKernel(1, -2));
    }

    public function testTriweight() : void
    {
        self::assertEquals(35 / 32, Kernel2D::triweightKernel(1, 0));
        self::assertEquals(0.0, Kernel2D::triweightKernel(1, -1));
        self::assertEquals(0.0, Kernel2D::triweightKernel(1, 1));

        self::assertEquals(0.0, Kernel2D::triweightKernel(1, 2));
        self::assertEquals(0.0, Kernel2D::triweightKernel(1, -2));
    }

    public function testTricube() : void
    {
        self::assertEquals(70 / 81, Kernel2D::tricubeKernel(1, 0));
        self::assertEquals(0.0, Kernel2D::tricubeKernel(1, -1));
        self::assertEquals(0.0, Kernel2D::tricubeKernel(1, 1));

        self::assertEquals(0.0, Kernel2D::tricubeKernel(1, 2));
        self::assertEquals(0.0, Kernel2D::tricubeKernel(1, -2));
    }

    public function testGaussian() : void
    {
        self::assertEquals(1 / \sqrt(2 * \M_PI), Kernel2D::gaussianKernel(1, 0));
        self::assertEquals(0.0, Kernel2D::gaussianKernel(1, -1));
        self::assertEquals(0.0, Kernel2D::gaussianKernel(1, 1));

        self::assertEquals(0.0, Kernel2D::gaussianKernel(1, 2));
        self::assertEquals(0.0, Kernel2D::gaussianKernel(1, -2));
    }

    public function testCosine() : void
    {
        self::assertEquals(\M_PI / 4, Kernel2D::cosineKernel(1, 0));
        self::assertEquals(0.0, Kernel2D::cosineKernel(1, -1));
        self::assertEquals(0.0, Kernel2D::cosineKernel(1, 1));

        self::assertEquals(0.0, Kernel2D::cosineKernel(1, 2));
        self::assertEquals(0.0, Kernel2D::cosineKernel(1, -2));
    }

    public function testLogistic() : void
    {
        self::assertEquals(0.5, Kernel2D::logisticKernel(1, 0));
        self::assertEquals(0.0, Kernel2D::logisticKernel(1, -1));
        self::assertEquals(0.0, Kernel2D::logisticKernel(1, 1));

        self::assertEquals(0.0, Kernel2D::logisticKernel(1, 2));
        self::assertEquals(0.0, Kernel2D::logisticKernel(1, -2));
    }
}
