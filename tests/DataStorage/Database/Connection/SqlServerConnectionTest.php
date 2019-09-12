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

namespace phpOMS\tests\DataStorage\Database\Connection;

use phpOMS\DataStorage\Database\Connection\SqlServerConnection;
use phpOMS\DataStorage\Database\DatabaseStatus;

/**
 * @internal
 */
class SqlServerConnectionTest extends \PHPUnit\Framework\TestCase
{
    protected function setUp() : void
    {
        if (!\extension_loaded('pdo_sqlsrv')) {
            $this->markTestSkipped(
              'The Sqlsrv extension is not available.'
            );
        }
    }

    public function testConnect() : void
    {
        $psql = new SqlServerConnection($GLOBALS['CONFIG']['db']['core']['mssql']['admin']);
        self::assertEquals(DatabaseStatus::OK, $psql->getStatus());
        self::assertEquals($GLOBALS['CONFIG']['db']['core']['mssql']['admin']['database'], $psql->getDatabase());
        self::assertEquals($GLOBALS['CONFIG']['db']['core']['mssql']['admin']['host'], $psql->getHost());
        self::assertEquals((int) $GLOBALS['CONFIG']['db']['core']['mssql']['admin']['port'], $psql->getPort());
        self::assertInstanceOf('\phpOMS\DataStorage\Database\Query\Grammar\SqlServerGrammar', $psql->getGrammar());
    }

    public function testInvalidDatabaseType() : void
    {
        self::expectException(\phpOMS\DataStorage\Database\Exception\InvalidConnectionConfigException::class);

        $db = $GLOBALS['CONFIG']['db']['core']['mssql']['admin'];
        unset($db['db']);
        $psql = new SqlServerConnection($db);
    }

    public function testInvalidHost() : void
    {
        self::expectException(\phpOMS\DataStorage\Database\Exception\InvalidConnectionConfigException::class);

        $db = $GLOBALS['CONFIG']['db']['core']['mssql']['admin'];
        unset($db['host']);
        $psql = new SqlServerConnection($db);
    }

    public function testInvalidPort() : void
    {
        self::expectException(\phpOMS\DataStorage\Database\Exception\InvalidConnectionConfigException::class);

        $db = $GLOBALS['CONFIG']['db']['core']['mssql']['admin'];
        unset($db['port']);
        $psql = new SqlServerConnection($db);
    }

    public function testInvalidDatabase() : void
    {
        self::expectException(\phpOMS\DataStorage\Database\Exception\InvalidConnectionConfigException::class);

        $db = $GLOBALS['CONFIG']['db']['core']['mssql']['admin'];
        unset($db['database']);
        $psql = new SqlServerConnection($db);
    }

    public function testInvalidLogin() : void
    {
        self::expectException(\phpOMS\DataStorage\Database\Exception\InvalidConnectionConfigException::class);

        $db = $GLOBALS['CONFIG']['db']['core']['mssql']['admin'];
        unset($db['login']);
        $psql = new SqlServerConnection($db);
    }

    public function testInvalidPassword() : void
    {
        self::expectException(\phpOMS\DataStorage\Database\Exception\InvalidConnectionConfigException::class);

        $db = $GLOBALS['CONFIG']['db']['core']['mssql']['admin'];
        unset($db['password']);
        $psql = new SqlServerConnection($db);
    }

    public function testInvalidDatabaseTypeName() : void
    {
        self::expectException(\phpOMS\DataStorage\Database\Exception\InvalidConnectionConfigException::class);

        $db       = $GLOBALS['CONFIG']['db']['core']['mssql']['admin'];
        $db['db'] = 'invalid';
        $psql = new SqlServerConnection($db);
    }

    public function testInvalidDatabaseName() : void
    {
        self::expectException(\phpOMS\DataStorage\Database\Exception\InvalidConnectionConfigException::class);

        $db = $GLOBALS['CONFIG']['db']['core']['mssql']['admin'];
        $db['database'] = 'invalid';

        $mysql = new SqlServerConnection($db);
    }
}
