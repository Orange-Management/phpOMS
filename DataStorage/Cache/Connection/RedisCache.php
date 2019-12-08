<?php
/**
 * Orange Management
 *
 * PHP Version 7.4
 *
 * @package   phpOMS\DataStorage\Cache\Connection
 * @copyright Dennis Eichhorn
 * @license   OMS License 1.0
 * @version   1.0.0
 * @link      https://orange-management.org
 */
declare(strict_types=1);

namespace phpOMS\DataStorage\Cache\Connection;

use phpOMS\DataStorage\Cache\CacheStatus;
use phpOMS\DataStorage\Cache\CacheType;
use phpOMS\DataStorage\Cache\Exception\InvalidConnectionConfigException;
use phpOMS\Stdlib\Base\Exception\InvalidEnumValue;
use phpOMS\DataStorage\Cache\Connection\CacheValueType;

/**
 * RedisCache class.
 *
 * @package phpOMS\DataStorage\Cache\Connection
 * @license OMS License 1.0
 * @link    https://orange-management.org
 * @since   1.0.0
 */
class RedisCache extends ConnectionAbstract
{
    /**
     * {@inheritdoc}
     */
    protected string $type = CacheType::REDIS;

    /**
     * Delimiter for cache meta data
     *
     * @var   string
     * @since 1.0.0
     */
    private const DELIM = '$';

    /**
     * Constructor
     *
     * @param array $data Cache data
     *
     * @since 1.0.0
     */
    public function __construct(array $data)
    {
        $this->con = new \Redis();
        $this->connect($data);
    }

    /**
     * {@inheritdoc}
     */
    public function connect(array $data) : void
    {
        $this->dbdata = isset($data) ? $data : $this->dbdata;

        if (!isset($this->dbdata['host'], $this->dbdata['port'], $this->dbdata['db'])) {
            $this->status = CacheStatus::FAILURE;
            throw new InvalidConnectionConfigException((string) \json_encode($this->dbdata));
        }

        $this->con->connect($this->dbdata['host'], $this->dbdata['port']);

        try {
            $this->con->ping();
        } catch (\Throwable $e) {
            $this->status = CacheStatus::FAILURE;
            return;
        }

        $this->con->setOption(\Redis::OPT_SERIALIZER, (string) \Redis::SERIALIZER_NONE);
        $this->con->setOption(\Redis::OPT_SCAN, (string) \Redis::SCAN_NORETRY);
        $this->con->select($this->dbdata['db']);

        $this->status = CacheStatus::OK;
    }

    /**
     * {@inheritdoc}
     */
    public function close() : void
    {
        if ($this->con !== null) {
            $this->con->close();
        }

        parent::close();
    }

    /**
     * {@inheritdoc}
     */
    public function set($key, $value, int $expire = -1) : void
    {
        if ($this->status !== CacheStatus::OK) {
            return;
        }

        if ($expire > 0) {
            $this->con->set($key, $this->build($value), $expire);
        }

        $this->con->set($key, $this->build($value));
    }

    /**
     * {@inheritdoc}
     */
    public function add($key, $value, int $expire = -1) : bool
    {
        if ($this->status !== CacheStatus::OK) {
            return false;
        }

        if ($expire > 0) {
            return $this->con->setNx($key, $this->build($value), $expire);
        }

        return $this->con->setNx($key, $this->build($value));
    }

    /**
     * {@inheritdoc}
     */
    public function get($key, int $expire = -1)
    {
        if ($this->status !== CacheStatus::OK || $this->con->exists($key) < 1) {
            return null;
        }

        $result = $this->con->get($key);

        if (\is_string($result)) {
            $type   = (int) $result[0];
            $start  = (int) \strpos($result, self::DELIM);
            $result = $this->reverseValue($type, $result, $start);
        }

        return $result;
    }

    /**
     * {@inheritdoc}
     */
    public function delete($key, int $expire = -1) : bool
    {
        if ($this->status !== CacheStatus::OK) {
            return false;
        }

        return $this->con->delete($key) > 0;
    }

    /**
     * {@inheritdoc}
     */
    public function flush(int $expire = 0) : bool
    {
        if ($this->status !== CacheStatus::OK) {
            return false;
        }

        $this->con->flushDb();

        return true;
    }

    /**
     * {@inheritdoc}
     */
    public function flushAll() : bool
    {
        if ($this->status !== CacheStatus::OK) {
            return false;
        }

        $this->con->flushDb();

        return true;
    }

    /**
     * {@inheritdoc}
     */
    public function replace($key, $value, int $expire = -1) : bool
    {
        if ($this->status !== CacheStatus::OK) {
            return false;
        }

        if ($this->con->exists($key) > 0) {
            $this->set($key, $this->build($value), $expire);

            return true;
        }

        return false;
    }

    /**
     * {@inheritdoc}
     */
    public function stats() : array
    {
        if ($this->status !== CacheStatus::OK) {
            return [];
        }

        $info = $this->con->info();

        $stats           = [];
        $stats['status'] = $this->status;
        $stats['count']  = $this->con->dbSize();
        $stats['size']   = $info['used_memory'];

        return $stats;
    }

    /**
     * {@inheritdoc}
     */
    public function getThreshold() : int
    {
        return 0;
    }

    /**
     * Destructor.
     *
     * @since 1.0.0
     */
    public function __destruct()
    {
        $this->close();
    }

    /**
     * Removing all cache elements larger or equal to the expiration date. Call flushAll for removing persistent cache elements (expiration is negative) as well.
     *
     * @param mixed $value Data to cache
     *
     * @return mixed
     *
     * @since 1.0.0
     */
    private function build($value)
    {
        $type = $this->dataType($value);
        $raw  = $this->cachify($value, $type);

        return \is_string($raw) ? $type . self::DELIM . $raw : $raw;
    }

    /**
     * Create string representation of data for storage
     *
     * @param mixed $value Value of the data
     * @param int   $type  Type of the cache data
     *
     * @return mixed
     *
     * @throws InvalidEnumValue This exception is thrown if an unsupported cache value type is used
     *
     * @since 1.0.0
     */
    private function cachify($value, int $type)
    {
        if ($type === CacheValueType::_INT || $type === CacheValueType::_STRING || $type === CacheValueType::_BOOL) {
            return (string) $value;
        } elseif ($type === CacheValueType::_FLOAT) {
            return \rtrim(\rtrim(\number_format($value, 5, '.', ''), '0'), '.');
        } elseif ($type === CacheValueType::_ARRAY) {
            return (string) \json_encode($value);
        } elseif ($type === CacheValueType::_SERIALIZABLE) {
            return \get_class($value) . self::DELIM . $value->serialize();
        } elseif ($type === CacheValueType::_JSONSERIALIZABLE) {
            return \get_class($value) . self::DELIM . ((string) \json_encode($value->jsonSerialize()));
        } elseif ($type === CacheValueType::_NULL) {
            return '';
        }

        throw new InvalidEnumValue($type);
    }

    /**
     * Parse cached value
     *
     * @param int   $type  Cached value type
     * @param mixed $raw   Cached value
     * @param int   $start Value start position
     *
     * @return mixed
     *
     * @since 1.0.0
     */
    private function reverseValue(int $type, $raw, int $start)
    {
        var_dump($raw);
        switch ($type) {
            case CacheValueType::_INT:
                return (int) \substr($raw, $start + 1);
            case CacheValueType::_FLOAT:
                return (float) \substr($raw, $start + 1);
            case CacheValueType::_BOOL:
                return (bool) \substr($raw, $start + 1);
            case CacheValueType::_STRING:
                return \substr($raw, $start + 1);
            case CacheValueType::_ARRAY:
                $array = \substr($raw, $start + 1);
                return \json_decode($array === false ? '[]' : $array, true);
            case CacheValueType::_NULL:
                return null;
            case CacheValueType::_JSONSERIALIZABLE:
                $namespaceStart = (int) \strpos($raw, self::DELIM, $start);
                $namespaceEnd   = (int) \strpos($raw, self::DELIM, $namespaceStart + 1);
                $namespace      = \substr($raw, $namespaceStart + 1, $namespaceEnd - $namespaceStart - 1);

                if ($namespace === false) {
                    return null;
                }

                return new $namespace();
            case CacheValueType::_SERIALIZABLE:
                $namespaceStart = (int) \strpos($raw, self::DELIM, $start);
                $namespaceEnd   = (int) \strpos($raw, self::DELIM, $namespaceStart + 1);
                $namespace      = \substr($raw, $namespaceStart + 1, $namespaceEnd - $namespaceStart - 1);

                if ($namespace === false) {
                    return null;
                }

                $obj = new $namespace();
                $obj->unserialize(\substr($raw, $namespaceEnd + 1));

                return $obj;
            default:
                return null;
        }
    }
}
