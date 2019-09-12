<?php
/**
 * Orange Management
 *
 * PHP Version 7.4
 *
 * @package   phpOMS\DataStorage
 * @copyright Dennis Eichhorn
 * @license   OMS License 1.0
 * @version   1.0.0
 * @link      https://orange-management.org
 */
declare(strict_types=1);

namespace phpOMS\DataStorage;

/**
 * Database connection interface.
 *
 * @package phpOMS\DataStorage
 * @license OMS License 1.0
 * @link    https://orange-management.org
 * @since   1.0.0
 */
interface DataStorageConnectionInterface
{

    /**
     * Get prefix.
     *
     * @return string
     *
     * @since 1.0.0
     */
    public function getPrefix() : string;

    /**
     * Connect to datastorage.
     *
     * Overwrites current connection if existing
     *
     * @param string[] $data the basic datastorage information for establishing a connection
     *
     * @return void
     *
     * @todo make private, reason was that not everyone wants to connect during initialization?!
     *
     * @since 1.0.0
     */
    public function connect(array $data) : void;

    /**
     * Get the datastorage type.
     *
     * @return string
     *
     * @since 1.0.0
     */
    public function getType() : string;

    /**
     * Get the datastorage status.
     *
     * @return int
     *
     * @since 1.0.0
     */
    public function getStatus() : int;

    /**
     * Close datastorage connection.
     *
     * @return void
     *
     * @since 1.0.0
     */
    public function close() : void;
}
