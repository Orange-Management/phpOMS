<?php
/**
 * Orange Management
 *
 * PHP Version 7.4
 *
 * @package   phpOMS\Algorithm\Sort;
 * @copyright Dennis Eichhorn
 * @license   OMS License 1.0
 * @version   1.0.0
 * @link      https://orange-management.org
 */
declare(strict_types=1);

namespace phpOMS\Algorithm\Sort;

/**
 * QuickSort class.
 *
 * @package phpOMS\Algorithm\Sort;
 * @license OMS License 1.0
 * @link    https://orange-management.org
 * @since   1.0.0
 */
class QuickSort implements SortInterface
{
    public static function sort(array $list, int $order = SortOrder::ASC) : array
    {
        $copy = $list;
        self::qsort($copy, 0, \count($list) - 1, $order);

        return $copy;
    }

    private static function qsort(array &$list, int $lo, int $hi, int $order) : void
    {
        if ($lo < $hi) {
            $i = self::partition($list, $lo, $hi, $order);
            self::qsort($list, $lo, $i, $order);
            self::qsort($list, $i + 1, $hi, $order);
        }
    }

    private static function partition(array &$list, int $lo, int $hi, int $order) : int
    {
        $pivot = $list[$lo + ((int) (($hi - $lo) / 2))];
        while (true) {
            while (!$list[$lo]->compare($pivot, $order)) {
                ++$lo;
            }

            while ($list[$hi]->compare($pivot, $order)) {
                --$hi;
            }

            if ($lo >= $hi) {
                return $hi;
            }

            $old       = $list[$lo];
            $list[$lo] = $list[$hi];
            $list[$hi] = $old;

            ++$lo;
            --$hi;
        }
    }
}
