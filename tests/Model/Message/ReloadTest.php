<?php
/**
 * Karaka
 *
 * PHP Version 8.0
 *
 * @package    tests
 * @copyright  2013 Dennis Eichhorn
 * @license    OMS License 1.0
 * @version    1.0.0
 * @link       https://karaka.app
 */
declare(strict_types=1);

namespace phpOMS\tests\phpOMS\Model\Message;

use phpOMS\Model\Message\Reload;

/**
 * @internal
 */
final class ReloadTest extends \PHPUnit\Framework\TestCase
{
    /**
     * @covers phpOMS\Model\Message\Reload
     * @group framework
     */
    public function testAttributes() : void
    {
        $obj = new Reload();
        self::assertInstanceOf('\phpOMS\Model\Message\Reload', $obj);

        /* Testing members */
        self::assertObjectHasAttribute('delay', $obj);
    }

    /**
     * @covers phpOMS\Model\Message\Reload
     * @group framework
     */
    public function testDefault() : void
    {
        $obj = new Reload();

        /* Testing default values */
        self::assertEquals(0, $obj->toArray()['time']);
    }

    /**
     * @covers phpOMS\Model\Message\Reload
     * @group framework
     */
    public function testSetGet() : void
    {
        $obj = new Reload(5);

        self::assertEquals(['type' => 'reload', 'time' => 5], $obj->toArray());
        self::assertEquals(\json_encode(['type' => 'reload', 'time' => 5]), $obj->__serialize());
        self::assertEquals(['type' => 'reload', 'time' => 5], $obj->jsonSerialize());

        $obj->setDelay(6);
        self::assertEquals(['type' => 'reload', 'time' => 6], $obj->toArray());

        $obj2 = new Reload();
        $obj2->__unserialize($obj->__serialize());
        self::assertEquals($obj, $obj2);
    }
}
