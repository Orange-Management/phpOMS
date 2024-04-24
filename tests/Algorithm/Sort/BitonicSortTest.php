<?php
/**
 * Jingga
 *
 * PHP Version 8.2
 *
 * @package   tests
 * @copyright Dennis Eichhorn
 * @license   OMS License 2.0
 * @version   1.0.0
 * @link      https://jingga.app
 */
declare(strict_types=1);

namespace phpOMS\tests\Algorithm\Sort;

use phpOMS\Algorithm\Sort\BitonicSort;
use phpOMS\Algorithm\Sort\SortOrder;

require_once __DIR__ . '/../../Autoloader.php';

/**
 * @internal
 */
#[\PHPUnit\Framework\Attributes\TestDox('phpOMS\tests\Algorithm\Sort\BitonicSortTest: Bitonic sort')]
final class BitonicSortTest extends \PHPUnit\Framework\TestCase
{
    protected $list = [];

    /**
     * {@inheritdoc}
     */
    protected function setUp() : void
    {
        $this->list = [
            new NumericElement(5),
            new NumericElement(1),
            new NumericElement(4),
            new NumericElement(2),
        ];
    }

    #[\PHPUnit\Framework\Attributes\Group('framework')]
    #[\PHPUnit\Framework\Attributes\TestDox('A list with one element returns the list with the element itself')]
    public function testSmallList() : void
    {
        $smallList = [new NumericElement(3)];
        $newList   = BitonicSort::sort($smallList);

        self::assertEquals($smallList, $newList);
    }

    #[\PHPUnit\Framework\Attributes\Group('framework')]
    #[\PHPUnit\Framework\Attributes\TestDox('A list ot elements can be sorted in ASC order')]
    public function testSortASC() : void
    {
        $newList = BitonicSort::sort($this->list);
        self::assertEquals(
            [1, 2, 4, 5], [$newList[0]->value, $newList[1]->value, $newList[2]->value, $newList[3]->value]
        );

        self::assertEquals(
            [5, 1, 4, 2], [$this->list[0]->value, $this->list[1]->value, $this->list[2]->value, $this->list[3]->value]
        );
    }

    #[\PHPUnit\Framework\Attributes\Group('framework')]
    #[\PHPUnit\Framework\Attributes\TestDox('A list ot elements can be sorted in DESC order')]
    public function testSortDESC() : void
    {
        $newList = BitonicSort::sort($this->list, SortOrder::DESC);
        self::assertEquals(
            [5, 4, 2, 1], [$newList[0]->value, $newList[1]->value, $newList[2]->value, $newList[3]->value]
        );

        self::assertEquals(
            [5, 1, 4, 2], [$this->list[0]->value, $this->list[1]->value, $this->list[2]->value, $this->list[3]->value]
        );
    }
}
