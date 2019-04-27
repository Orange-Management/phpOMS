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
 declare(strict_types=1);

namespace phpOMS\tests\Validation\Finance;

use phpOMS\Validation\Finance\IbanEnum;

/**
 * @internal
 */
class IbanEnumTest extends \PHPUnit\Framework\TestCase
{
    public function testEnums() : void
    {
        $enums = IbanEnum::getConstants();
        $ok    = true;

        foreach ($enums as $enum) {
            $temp = \substr($enum, 2);

            if (\preg_match('/[^kbsxcinm0at\ ]/', $temp) === 1) {
                $ok = false;

                break;
            }
        }

        self::assertTrue($ok);
    }
}
