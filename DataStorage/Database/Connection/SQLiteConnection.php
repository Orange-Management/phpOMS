<?php
/**
 * Orange Management
 *
 * PHP Version 7.2
 *
 * @package    phpOMS\DataStorage\Database\Connection
 * @copyright  Dennis Eichhorn
 * @license    OMS License 1.0
 * @version    1.0.0
 * @link       http://website.orange-management.de
 */
declare(strict_types=1);

namespace phpOMS\DataStorage\Database\Connection;

use phpOMS\DataStorage\Database\DatabaseStatus;
use phpOMS\DataStorage\Database\DatabaseType;
use phpOMS\DataStorage\Database\Query\Grammar\SQLiteGrammar;

/**
 * Database handler.
 *
 * Handles the database connection.
 * Implementing wrapper functions for multiple databases is planned (far away).
 *
 * @package    phpOMS\DataStorage\Database\Connection
 * @license    OMS License 1.0
 * @link       http://website.orange-management.de
 * @since      1.0.0
 */
final class SQLiteConnection extends ConnectionAbstract
{

    /**
     * Object constructor.
     *
     * Creates the database object and overwrites all default values.
     *
     * @param string[] $dbdata the basic database information for establishing a connection
     *
     * @since  1.0.0
     */
    public function __construct(array $dbdata)
    {
        $this->type    = DatabaseType::SQLITE;
        $this->grammar = new SQLiteGrammar();
        $this->connect($dbdata);
    }

    /**
     * {@inheritdoc}
     */
    public function connect(array $dbdata = null) : void
    {
        $this->close();

        $this->dbdata = $dbdata !== null ? $dbdata : $this->dbdata;
        $this->prefix = $dbdata['prefix'];

        try {
            $this->con = new \PDO($this->dbdata['db'] . ':' . $this->dbdata['path']);
            $this->con->setAttribute(\PDO::ATTR_EMULATE_PREPARES, false);
            $this->con->setAttribute(\PDO::ATTR_ERRMODE, \PDO::ERRMODE_EXCEPTION);

            $this->status = DatabaseStatus::OK;
        } catch (\PDOException $e) {
            $this->status = DatabaseStatus::MISSING_DATABASE;
            $this->con    = null;
        } finally {
            $this->dbdata['password'] = '****';
        }
    }
}
