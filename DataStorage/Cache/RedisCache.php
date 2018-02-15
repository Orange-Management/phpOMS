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
declare(strict_types=1);

namespace phpOMS\DataStorage\Cache;

/**
 * RedisCache class.
 *
 * PHP Version 5.6
 *
 * @package    Framework
 * @license    OMS License 1.0
 * @link       http://website.orange-management.de
 * @since      1.0.0
 */
class RedisCache implements CacheInterface
{

    /**
     * {@inheritdoc}
     */
    public function set($key, $value, int $expire = -1) /* : void */
    {
        // TODO: Implement set() method.
    }

    /**
     * {@inheritdoc}
     */
    public function add($key, $value, int $expire = -1) : bool
    {
        // TODO: Implement add() method.
    }

    /**
     * {@inheritdoc}
     */
    public function get($key, int $expire = -1)
    {
        // TODO: Implement get() method.
    }

    /**
     * {@inheritdoc}
     */
    public function delete($key, int $expire = -1) : bool
    {
        // TODO: Implement delete() method.
    }

    /**
     * {@inheritdoc}
     */
    public function flush(int $expire = 0) : bool
    {
        // TODO: Implement flush() method.

        return true;
    }

    /**
     * {@inheritdoc}
     */
    public function flushAll() : bool
    {
        // TODO: Implement flush() method.

        return true;
    }

    /**
     * {@inheritdoc}
     */
    public function replace($key, $value, int $expire = -1) : bool
    {
        // TODO: Implement replace() method.
    }

    /**
     * {@inheritdoc}
     */
    public function stats() : array
    {
        // TODO: Implement stats() method.
    }

    /**
     * {@inheritdoc}
     */
    public function getThreshold() : int
    {
        // TODO: Implement getThreshold() method.
    }

    /**
     * {@inheritdoc}
     */
    public function setStatus(int $status) /* : void */
    {
        // TODO: Implement setStatus() method.
    }
}
