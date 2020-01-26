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

namespace phpOMS\tests\Math\Number;

use phpOMS\Math\Topology\Metrics2D;
use phpOMS\Math\Topology\MetricsND;

/**
 * @testdox phpOMS\tests\Math\Topology\MetricsNDTest: Metric/distance calculations
 *
 * @internal
 */
class MetricsNDTest extends \PHPUnit\Framework\TestCase
{
    /**
     * @testdox The manhattan distance can be calculated
     * @covers phpOMS\Math\Topology\MetricsND
     * @group framework
     */
    public function testManhattan() : void
    {
        self::assertEquals(
            MetricsND::manhattan(['x' => 0, 'y' => 3], ['x' => 7, 'y' => 6]),
            MetricsND::manhattan(['x' => 0, 'y' => 3], ['x' => 7, 'y' => 6])
        );
    }

    /**
     * @testdox The euclidean distance can be calculated
     * @covers phpOMS\Math\Topology\MetricsND
     * @group framework
     */
    public function testEuclidean() : void
    {
        self::assertEqualsWithDelta(
            Metrics2D::euclidean(['x' => 0, 'y' => 3], ['x' => 7, 'y' => 6]),
            MetricsND::euclidean(['x' => 0, 'y' => 3], ['x' => 7, 'y' => 6]),
            0.1
        );
    }

    /**
     * @testdox The chebyshev distance can be calculated
     * @covers phpOMS\Math\Topology\MetricsND
     * @group framework
     */
    public function testChebyshev() : void
    {
        self::assertEquals(
            MetricsND::chebyshev(['x' => 0, 'y' => 3], ['x' => 7, 'y' => 6]),
            MetricsND::chebyshev(['x' => 0, 'y' => 3], ['x' => 7, 'y' => 6])
        );
    }

    /**
     * @testdox The minkowski distance can be calculated
     * @covers phpOMS\Math\Topology\MetricsND
     * @group framework
     */
    public function testMinkowski() : void
    {
        self::assertEqualsWithDelta(
            Metrics2D::minkowski(['x' => 0, 'y' => 3], ['x' => 7, 'y' => 6], 3),
            MetricsND::minkowski(['x' => 0, 'y' => 3], ['x' => 7, 'y' => 6], 3),
            0.1
        );
    }

    /**
     * @testdox The canberra distance can be calculated
     * @covers phpOMS\Math\Topology\MetricsND
     * @group framework
     */
    public function testCanberra() : void
    {
        self::assertEqualsWithDelta(
            Metrics2D::canberra(['x' => 0, 'y' => 3], ['x' => 7, 'y' => 6]),
            MetricsND::canberra(['x' => 0, 'y' => 3], ['x' => 7, 'y' => 6]),
            0.1
        );
    }

    /**
     * @testdox The bray-curtis distance can be calculated
     * @covers phpOMS\Math\Topology\MetricsND
     * @group framework
     */
    public function testBrayCurtis() : void
    {
        self::assertEqualsWithDelta(
            Metrics2D::brayCurtis(['x' => 0, 'y' => 3], ['x' => 7, 'y' => 6]),
            MetricsND::brayCurtis(['x' => 0, 'y' => 3], ['x' => 7, 'y' => 6]),
            0.1
        );
    }

    /**
     * @testdox The angular distance can be calculated
     * @covers phpOMS\Math\Topology\MetricsND
     * @group framework
     */
    public function testAngularSeparation() : void
    {
        self::assertEqualsWithDelta(
            Metrics2D::angularSeparation(['x' => 0, 'y' => 3], ['x' => 7, 'y' => 6]),
            MetricsND::angularSeparation(['x' => 0, 'y' => 3], ['x' => 7, 'y' => 6]),
            0.1
        );
    }

    /**
     * @testdox The hamming distance can be calculated
     * @covers phpOMS\Math\Topology\MetricsND
     * @group framework
     */
    public function testHammingDistance() : void
    {
        self::assertEquals(
            MetricsND::hamming([1, 1, 1, 1], [0, 1, 0, 0]),
            MetricsND::hamming([1, 1, 1, 1], [0, 1, 0, 0]),
        );
    }

    /**
     * @testdox Different dimension sizes for the coordinates in the hamming metric throw a InvalidDimensionException
     * @covers phpOMS\Math\Topology\MetricsND
     * @group framework
     */
    public function testInvalidHammingDimension() : void
    {
        self::expectException(\phpOMS\Math\Matrix\Exception\InvalidDimensionException::class);

        Metrics2D::ulam([3, 6, 4], [4, 6, 8, 3]);
    }
}