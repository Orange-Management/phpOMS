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

namespace phpOMS\tests\Math\Matrix;

use phpOMS\Math\Matrix\Matrix;
use phpOMS\Math\Matrix\Vector;
use phpOMS\Math\Matrix\LUDecomposition;

class LUDecompositionTest extends \PHPUnit\Framework\TestCase
{
    public function testDecomposition()
    {
        $B = new Matrix();
        $B->setMatrix([
            [25, 15, -5],
            [15, 17, 0],
            [-5, 0, 11],
        ]);

        $lu = new LUDecomposition($B);

        self::assertEquals([
            [1, 0, 0],
            [0.6, 1, 0],
            [-0.2, 0.375, 1],
        ], $lu->getL()->toArray(), '', 0.2);

        self::assertEquals([
            [25, 15, -5],
            [0, 8, 3],
            [0, 0, 8.875],
        ], $lu->getU()->toArray(), '', 0.2);

        $vec = new Vector();
        $vec->setMatrix([[40], [49], [28]]);
        self::assertTrue($lu->isNonSingular());
        self::assertEquals([[1], [2], [3]], $lu->solve($vec)->toArray(), '', 0.2);
        self::assertEquals([0, 1, 2], $lu->getPivot());
    }

    public function testSingularMatrix()
    {
        $B = new Matrix();
        $B->setMatrix([
            [25, 15, -5],
            [0, 0, 1],
            [0, 0, 2],
        ]);

        $lu = new LUDecomposition($B);

        self::assertFalse($lu->isNonSingular());
    }

    /**
     * @expectedException \Exception
     */
    public function testSolveOfSingularMatrix()
    {
        $B = new Matrix();
        $B->setMatrix([
            [25, 15, -5],
            [0, 0, 1],
            [0, 0, 2],
        ]);

        $lu = new LUDecomposition($B);

        $vec = new Vector();
        $vec->setMatrix([[40], [49], [28]]);

        $lu->solve($vec);
    }

    public function testComposition()
    {
        $A = new Matrix();
        $A->setMatrix([
            [25, 15, -5],
            [15, 17, 0],
            [-5, 0, 11],
        ]);

        $lu = new LUDecomposition($A);

        self::assertEquals(
            $A->toArray(),
            $lu->getL()
                ->mult($lu->getU())
                ->toArray(),
            '', 0.2
        );
    }

    /**
     * @expectedException \phpOMS\Math\Matrix\Exception\InvalidDimensionException
     */
    public function testInvalidDimension()
    {
        $B = new Matrix();
        $B->setMatrix([
            [25, 15, -5],
            [15, 17, 0],
            [-5, 0, 11],
        ]);

        $lu  = new LUDecomposition($B);
        $vec = new Vector();
        $vec->setMatrix([[40], [49]]);

        $lu->solve($vec);
    }
}
