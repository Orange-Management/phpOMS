<?php
/**
 * Orange Management
 *
 * PHP Version 8.0
 *
 * @package   tests
 * @copyright Dennis Eichhorn
 * @license   OMS License 1.0
 * @version   1.0.0
 * @link      https://orange-management.org
 */
declare(strict_types=1);

namespace phpOMS\tests\Utils\Git;

use phpOMS\Utils\Git\Git;

/**
 * @testdox phpOMS\tests\Utils\Git\GitTest: Git utilities
 *
 * @internal
 */
class GitTest extends \PHPUnit\Framework\TestCase
{
    /**
     * @testdox The git path can be returned
     * @covers phpOMS\Utils\Git\Git
     * @group framework
     */
    public function testBinary() : void
    {
        self::assertEquals('/usr/bin/git', Git::getBin());
    }
}
