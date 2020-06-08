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

namespace phpOMS\tests\Math\Stochastic\Distribution;

use phpOMS\Math\Stochastic\Distribution\ExponentialDistribution;

/**
 * @internal
 */
class ExponentialDistributionTest extends \PHPUnit\Framework\TestCase
{
    public function testPdf() : void
    {
        $lambda = 0.1;
        $x      = 7;

        self::assertEqualsWithDelta(0.049659, ExponentialDistribution::getPdf($x, $lambda), 0.01);
    }

    public function testCdf() : void
    {
        $lambda = 0.1;
        $x      = 7;

        self::assertEqualsWithDelta(0.5034, ExponentialDistribution::getCdf($x, $lambda), 0.01);
    }

    public function testMean() : void
    {
        self::assertEquals(1/3, ExponentialDistribution::getMean(3));
    }

    public function testMode() : void
    {
        self::assertEquals(0, ExponentialDistribution::getMode());
    }

    public function testMedian() : void
    {
        self::assertEquals(1/3 * \log(2), ExponentialDistribution::getMedian(3));
    }

    public function testMgf() : void
    {
        $lambda = 3;
        $t      = 2;

        self::assertEquals($lambda / ($lambda - $t), ExponentialDistribution::getMgf($t, $lambda));
    }

    public function testVariance() : void
    {
        self::assertEquals(1/(3 ** 2), ExponentialDistribution::getVariance(3));
    }

    public function testStandardDeviation() : void
    {
        self::assertEquals(\sqrt(1/(3 ** 2)), ExponentialDistribution::getStandardDeviation(3));
    }

    public function testExKurtosis() : void
    {
        self::assertEquals(6, ExponentialDistribution::getExKurtosis());
    }

    public function testSkewness() : void
    {
        self::assertEquals(2, ExponentialDistribution::getSkewness());
    }

    public function testMgfException() : void
    {
        $this->expectException(\OutOfBoundsException::class);

        ExponentialDistribution::getMgf(3, 3);
    }
}
