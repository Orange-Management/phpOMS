<?php
/**
 * Orange Management
 *
 * PHP Version 7.1
 *
 * @package    TBD
 * @copyright  Dennis Eichhorn
 * @license    OMS License 1.0
 * @version    1.0.0
 * @link       http://website.orange-management.de
 */

namespace Tests\PHPUnit\phpOMS\Math\Matrix\Exception;

require_once __DIR__ . '/../../../../../../phpOMS/Autoloader.php';

use phpOMS\Math\Matrix\Exception\InvalidDimensionException;

class InvalidDimensionExceptionTest extends \PHPUnit\Framework\TestCase
{
    public function testException()
    {
        self::assertInstanceOf(\UnexpectedValueException::class, new InvalidDimensionException(''));
    }
}
