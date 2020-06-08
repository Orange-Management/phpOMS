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

use phpOMS\Math\Stochastic\Distribution\UniformDistributionDiscrete;

/**
 * @internal
 */
class UniformDistributionDiscreteTest extends \PHPUnit\Framework\TestCase
{
    public function testPmf() : void
    {
        $a = 1;
        $b = 4;

        self::assertEquals(1 / ($b - $a + 1), UniformDistributionDiscrete::getPmf($a, $b));
    }

    public function testCdf() : void
    {
        $a = 1;
        $b = 4;
        $k = 3;

        self::assertEquals(($k - $a + 1) / ($b - $a + 1), UniformDistributionDiscrete::getCdf($k, $a, $b));
    }

    public function testSkewness() : void
    {
        self::assertEquals(0, UniformDistributionDiscrete::getSkewness());
    }

    public function testMean() : void
    {
        $a = 1;
        $b = 4;

        self::assertEquals(1 / 2 * ($a + $b), UniformDistributionDiscrete::getMean($a, $b));
    }

    public function testMedian() : void
    {
        $a = 1;
        $b = 4;

        self::assertEquals(1 / 2 * ($a + $b), UniformDistributionDiscrete::getMedian($a, $b));
    }

    public function testVariance() : void
    {
        $a = 1;
        $b = 4;

        self::assertEquals((($b - $a + 1) ** 2 - 1) / 12, UniformDistributionDiscrete::getVariance($a, $b));
    }

    public function testStandardDeviation() : void
    {
        $a = 1;
        $b = 4;

        self::assertEquals(\sqrt((($b - $a + 1) ** 2 - 1) / 12), UniformDistributionDiscrete::getStandardDeviation($a, $b));
    }

    public function testExKurtosis() : void
    {
        $a = 1;
        $b = 4;
        $n = $b - $a + 1;

        self::assertEquals(-(6 * ($n ** 2 + 1)) / (5 * ($n ** 2 - 1)), UniformDistributionDiscrete::getExKurtosis($a, $b));
    }

    public function testCdfExceptionUpper() : void
    {
        $this->expectException(\OutOfBoundsException::class);

        UniformDistributionDiscrete::getCdf(5, 2, 4);
    }

    public function testCdfExceptionLower() : void
    {
        $this->expectException(\OutOfBoundsException::class);

        UniformDistributionDiscrete::getCdf(1, 2, 4);
    }
}
