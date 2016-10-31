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
namespace phpOMS\DataStorage\Cache;

use phpOMS\DataStorage\Cache\CacheInterface;
use phpOMS\DataStorage\Cache\FileCache;


/**
 * Database connection factory.
 *
 * @category   Framework
 * @package    phpOMS\DataStorage\Database
 * @author     OMS Development Team <dev@oms.com>
 * @author     Dennis Eichhorn <d.eichhorn@oms.com>
 * @license    OMS License 1.0
 * @link       http://orange-management.com
 * @since      1.0.0
 */
class CacheFactory
{

    /**
     * Constructor.
     *
     * @since  1.0.0
     * @author Dennis Eichhorn <d.eichhorn@oms.com>
     */
    private function __construct()
    {
    }

    /**
     * Create cache connection.
     *
     * Overwrites current connection if existing
     *
     * @param string[] $cacheData the basic database information for establishing a connection
     *
     * @return CacheInterface
     *
     * @throws \InvalidArgumentException Throws this exception if the database is not supported.
     *
     * @since  1.0.0
     * @author Dennis Eichhorn <d.eichhorn@oms.com>
     */
    public static function create(array $cacheData) : CacheInterface
    {
        switch ($cacheData['type']) {
            case 'file':
                return new FileCache($cacheData);
                break;
            default:
                throw new \InvalidArgumentException('Cache "' . $cacheData['type'] . '" is not supported.');
        }
    }
}
