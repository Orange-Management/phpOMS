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
namespace phpOMS\Validation\Base;
use phpOMS\Validation\ValidatorAbstract;


/**
 * Validator abstract.
 *
 * @category   Validation
 * @package    Framework
 * @author     OMS Development Team <dev@oms.com>
 * @author     Dennis Eichhorn <d.eichhorn@oms.com>
 * @license    OMS License 1.0
 * @link       http://orange-management.com
 * @since      1.0.0
 */
abstract class Iban extends ValidatorAbstract
{
    /**
     * Constructor.
     *
     * @since  1.0.0
     * @author Dennis Eichhorn <d.eichhorn@oms.com>
     */
    public function __construct()
    {
    }

    /**
     * @param string $value Iban to validate
     *
     * @return bool
     *
     * @since  1.0.0
     * @author Dennis Eichhorn <d.eichhorn@oms.com>
     */
    public static function isValid($value) : bool
    {
        $value  = \phpOMS\Datatypes\Iban::normalize($value);
        $layout = str_replace(' ', '', IbanEnum::getByName($enumName = 'C_' . strtoupper(substr($value, 0, 2))));

        if (!IbanEnum::isValidName($enumName)) {
            self::$error = IbanErrorType::INVALID_COUNTRY;

            return false;
        }

        if (strlen($value) !== strlen($layout)) {
            self::$error = IbanErrorType::INVALID_LENGTH;

            return false;
        }

        if (!self::validateZeros($value, $layout)) {
            self::$error = IbanErrorType::EXPECTED_ZERO;

            return false;
        }

        if (!self::validateNumeric($value, $layout)) {
            self::$error = IbanErrorType::EXPECTED_NUMERIC;

            return false;
        }

        if (!self::validateChecksum($value)) {
            self::$error = IbanErrorType::INVALID_CHECKSUM;

            return false;
        }

        return true;
    }

    /**
     * Validate checksum
     *
     * @param string $iban Iban to validate
     *
     * @return bool
     *
     * @since  1.0.0
     * @author Dennis Eichhorn <d.eichhorn@oms.com>
     */
    private static function validateChecksum(string $iban) : bool
    {
        $chars      = ['a' => 10, 'b' => 11, 'c' => 12, 'd' => 13, 'e' => 14, 'f' => 15, 'g' => 16, 'h' => 17, 'i' => 18,
                       'j' => 19, 'k' => 20, 'l' => 21, 'm' => 22, 'n' => 23, 'o' => 24, 'p' => 25, 'q' => 26, 'r' => 27,
                       's' => 28, 't' => 29, 'u' => 30, 'v' => 31, 'w' => 32, 'x' => 33, 'y' => 34, 'z' => 35,];
        $moved      = substr($iban, 4) . substr($iban, 0, 4);
        $movedArray = str_split($moved);
        $new        = '';

        // performing checksum calculation
        foreach ($movedArray as $key => $value) {
            if (!is_numeric($movedArray[$key])) {
                $movedArray[$key] = $chars[$movedArray[$key]];
            }

            $new .= $movedArray[$key];
        }

        $mod = bcmod($new, '97');

        if ($mod != 1) {
            self::$error = IbanErrorType::INVALID_CHECKSUM;
        }

        return $mod == 1;
    }

    /**
     * Validate positions that should have zeros
     *
     * @param string $iban   Iban to validate
     * @param string $layout Iban layout
     *
     * @return bool
     *
     * @since  1.0.0
     * @author Dennis Eichhorn <d.eichhorn@oms.com>
     */
    private static function validateZeros(string $iban, string $layout) : bool
    {
        if (strpos($layout, '0') === false) {
            return true;
        }

        $lastPos = 0;
        while (($lastPos = strpos($layout, '0', $lastPos)) !== false) {
            if ($iban[$lastPos] !== '0') {
                return false;
            }

            $lastPos += 1;
        }

        return true;
    }

    /**
     * Validate positions that should be numeric
     *
     * @param string $iban   Iban to validate
     * @param string $layout Iban layout
     *
     * @return bool
     *
     * @since  1.0.0
     * @author Dennis Eichhorn <d.eichhorn@oms.com>
     */
    private static function validateNumeric(string $iban, string $layout) : bool
    {
        if (strpos($layout, 'n') === false) {
            return true;
        }

        $lastPos = 0;
        while (($lastPos = strpos($layout, 'n', $lastPos)) !== false) {
            if (!is_numeric($iban[$lastPos])) {
                return false;
            }

            $lastPos += 1;
        }

        return true;
    }
}
