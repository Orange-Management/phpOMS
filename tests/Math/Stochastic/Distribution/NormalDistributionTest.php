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

namespace phpOMS\tests\Math\Stochastic\Distribution;

use phpOMS\Math\Stochastic\Distribution\NormalDistribution;

/**
 * @internal
 */
class NormalDistributionTest extends \PHPUnit\Framework\TestCase
{
    /**
     * @covers phpOMS\Math\Stochastic\Distribution\NormalDistribution
     * @group framework
     */
    public function testPdf() : void
    {
        $mean = 2;
        $sig  = 1;
        $x    = 3;

        self::assertEqualsWithDelta(0.24197, NormalDistribution::getPdf($x, $mean, $sig), 0.01);
    }

    /**
     * @covers phpOMS\Math\Stochastic\Distribution\NormalDistribution
     * @group framework
     */
    public function testCdf() : void
    {
        $mean = 2;
        $sig  = 1;
        $x    = 3;

        self::assertEqualsWithDelta(0.84134, NormalDistribution::getCdf($x, $mean, $sig), 0.01);
    }

    /**
     * @covers phpOMS\Math\Stochastic\Distribution\NormalDistribution
     * @group framework
     */
    public function testMean() : void
    {
        $mu = 4;

        self::assertEquals($mu, NormalDistribution::getMean($mu));
    }

    /**
     * @covers phpOMS\Math\Stochastic\Distribution\NormalDistribution
     * @group framework
     */
    public function testMedian() : void
    {
        $mu = 4;

        self::assertEquals($mu, NormalDistribution::getMedian($mu));
    }

    /**
     * @covers phpOMS\Math\Stochastic\Distribution\NormalDistribution
     * @group framework
     */
    public function testMode() : void
    {
        $mu = 4;

        self::assertEquals($mu, NormalDistribution::getMode($mu));
    }

    /**
     * @covers phpOMS\Math\Stochastic\Distribution\NormalDistribution
     * @group framework
     */
    public function testSkewness() : void
    {
        self::assertEquals(0, NormalDistribution::getSkewness());
    }

    /**
     * @covers phpOMS\Math\Stochastic\Distribution\NormalDistribution
     * @group framework
     */
    public function testExKurtosis() : void
    {
        self::assertEquals(0, NormalDistribution::getExKurtosis());
    }

    /**
     * @covers phpOMS\Math\Stochastic\Distribution\NormalDistribution
     * @group framework
     */
    public function testVariance() : void
    {
        $sig = 0.8;

        self::assertEquals($sig ** 2, NormalDistribution::getVariance($sig));
    }

    /**
     * @covers phpOMS\Math\Stochastic\Distribution\NormalDistribution
     * @group framework
     */
    public function testStandardDeviation() : void
    {
        $sig = 0.8;

        self::assertEquals($sig, NormalDistribution::getStandardDeviation($sig));
    }

    /**
     * @covers phpOMS\Math\Stochastic\Distribution\NormalDistribution
     * @group framework
     */
    public function testSampleSizeCalculation() : void
    {
        self::assertEqualsWithDelta(277.54, NormalDistribution::getSampleSizeFromPopulation(NormalDistribution::TABLE['0.95'], 0.05, 1000, 0.5), 0.01);
    }

    /**
     * @covers phpOMS\Math\Stochastic\Distribution\NormalDistribution
     * @group framework
     */
    public function testSampleSizeInfiniteCalculation() : void
    {
        self::assertEqualsWithDelta(384.16, NormalDistribution::getSampleSizeFromInfinitePopulation(NormalDistribution::TABLE['0.95'], 0.05, 0.5), 0.01);
    }

    /**
     * @covers phpOMS\Math\Stochastic\Distribution\NormalDistribution
     * @group framework
     */
    public function testMgf() : void
    {
        $t     = 3;
        $mu    = 4;
        $sigma = 5;

        self::assertEquals(
            \exp($mu * $t + $sigma ** 2 * $t ** 2 / 2),
            NormalDistribution::getMgf($t, $mu, $sigma)
        );
    }

    /**
     * @covers phpOMS\Math\Stochastic\Distribution\NormalDistribution
     * @group framework
     */
    public function testFisherInformation() : void
    {
        self::assertEquals(
            [
                [1 / 3 ** 2, 0],
                [0, 1 / (2 * 3 ** 4)],
            ],
            NormalDistribution::getFisherInformation(3)
        );
    }
}
