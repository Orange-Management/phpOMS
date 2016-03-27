<?php
/**
 * Orange Management
 *
 * PHP Version 7.0
 *
 * @category   TBD
 * @package    TBD
 * @author     OMS Development Team <dev@oms.com>
 * @author     Dennis Eichhorn <d.eichhorn@oms.com>
 * @copyright  2013 Dennis Eichhorn
 * @license    OMS License 1.0
 * @version    1.0.0
 * @link       http://orange-management.com
 */

namespace phpOMS\Math\Number;

/**
 * Well known functions class.
 *
 * @category   Framework
 * @package    phpOMS\DataStorage\Database
 * @author     OMS Development Team <dev@oms.com>
 * @author     Dennis Eichhorn <d.eichhorn@oms.com>
 * @license    OMS License 1.0
 * @link       http://orange-management.com
 * @since      1.0.0
 */
class Prime
{
    /**
     * Is mersenne number?
     *
     * @param int $n Number to test
     *
     * @return bool
     *
     * @since  1.0.0
     * @author Dennis Eichhorn
     */
    public static function isMersenne(int $n) : bool
    {
        $mersenne = log($n+1, 2);

        return $mersenne - (int) $mersenne < 0.00001;
    }

    /**
     * Get mersenne number
     *
     * @param int $p Power
     *
     * @return int
     *
     * @since  1.0.0
     * @author Dennis Eichhorn
     */
    public static function mersenne(int $p) : int
    {
        return 2**$p - 1;
    }

    /**
     * Is prime?
     *
     * @param int $n Number to test
     * @param int $k Accuracy
     *
     * @return bool
     *
     * @since  1.0.0
     * @author Dennis Eichhorn
     */
    public static function rabinTest(int $n, int $k) : bool
    {
        if ($n == 2) {
            return true;
        }

        if ($n < 2 || $n % 2 == 0){
            return false;
        }
     
        $d = $n - 1;
        $s = 0;
     
        while ($d % 2 == 0) {
            $d /= 2;
            $s++;
        }
     
        for ($i = 0; $i < $k; $i++) {
            $a = mt_rand(2, $n-1);
     
            $x = bcpowmod($a, $d, $n);

            if ($x == 1 || $x == $n-1) {
                continue;
            }
     
            for ($j = 1; $j < $s; $j++) {
                $x = bcmod(bcmul($x, $x), $n);

                if ($x == 1) {
                    return false;
                }

                if ($x == $n-1) {
                    continue 2;
                }
            }

            return false;
        }

        return true;
    }
}