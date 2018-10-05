<?php
/**
 * Orange Management
 *
 * PHP Version 7.2
 *
 * @package    phpOMS\DataStorage\Cache\Connection
 * @copyright  Dennis Eichhorn
 * @license    OMS License 1.0
 * @version    1.0.0
 * @link       http://website.orange-management.de
 */
declare(strict_types=1);

namespace phpOMS\DataStorage\Cache\Connection;

use phpOMS\DataStorage\Cache\CacheStatus;
use phpOMS\DataStorage\Cache\CacheType;
use phpOMS\Stdlib\Base\Exception\InvalidEnumValue;
use phpOMS\System\File\Local\Directory;
use phpOMS\System\File\Local\File;
use phpOMS\DataStorage\Cache\Exception\InvalidConnectionConfigException;

/**
 * MemCache class.
 *
 * PHP Version 7.2
 *
 * @package    phpOMS\DataStorage\Cache\Connection
 * @license    OMS License 1.0
 * @link       http://website.orange-management.de
 * @since      1.0.0
 */
class FileCache extends ConnectionAbstract
{
    /**
     * {@inheritdoc}
     */
    protected $type = CacheType::FILE;

    /**
     * Delimiter for cache meta data
     *
     * @var string
     * @since 1.0.0
     */
    private const DELIM = '$';

    /**
     * File path sanitizer
     *
     * @var string
     * @since 1.0.0
     */
    private const SANITIZE = '~';

    /**
     * Only cache if data is larger than threshold (0-100).
     *
     * @var int
     * @since 1.0.0
     */
    private $threshold = 50;

    /**
     * Constructor
     *
     * @param string $path Cache path
     *
     * @since  1.0.0
     */
    public function __construct(string $path)
    {
        $this->connect([$path]);
    }

    /**
     * {@inheritdoc}
     */
    public function connect(array $data) : void
    {
        $this->dbdata = $data;

        if (!Directory::exists($data[0])) {
            Directory::create($data[0], 0766, true);
        }

        if (\realpath($data[0]) === false) {
            $this->status = CacheStatus::FAILURE;
            throw new InvalidConnectionConfigException((string) \json_encode($this->dbdata));
        }

        $this->status = CacheStatus::OK;
        $this->con    = \realpath($data[0]);
    }

    /**
     * {@inheritdoc}
     */
    public function flushAll() : bool
    {
        if ($this->status !== CacheStatus::OK) {
            return false;
        }

        \array_map('unlink', \glob($this->con . '/*'));

        return true;
    }

    /**
     * {@inheritdoc}
     */
    public function stats() : array
    {
        if ($this->status !== CacheStatus::OK) {
            return [];
        }

        $stats           = [];
        $stats['status'] = $this->status;
        $stats['count']  = Directory::count($this->con);
        $stats['size']   = Directory::size($this->con);

        return $stats;
    }

    /**
     * {@inheritdoc}
     */
    public function getThreshold() : int
    {
        return $this->threshold;
    }

    /**
     * {@inheritdoc}
     */
    public function set($key, $value, int $expire = -1) : void
    {
        if ($this->status !== CacheStatus::OK) {
            return;
        }

        $path = Directory::sanitize($key, self::SANITIZE);

        File::put($this->con . '/' . \trim($path, '/') . '.cache', $this->build($value, $expire));
    }

    /**
     * {@inheritdoc}
     */
    public function add($key, $value, int $expire = -1) : bool
    {
        if ($this->status !== CacheStatus::OK) {
            return false;
        }

        $path = $this->getPath($key);

        if (!File::exists($path)) {
            File::put($path, $this->build($value, $expire));

            return true;
        }

        return false;
    }

    /**
     * Removing all cache elements larger or equal to the expiration date. Call flushAll for removing persistent cache elements (expiration is negative) as well.
     *
     * @param mixed $value  Data to cache
     * @param int   $expire Expire date of the cached data
     *
     * @return string
     *
     * @since  1.0.0
     */
    private function build($value, int $expire) : string
    {
        $type = $this->dataType($value);
        $raw  = $this->stringify($value, $type);

        return $type . self::DELIM . $expire . self::DELIM . $raw;
    }

    /**
     * Analyze caching data type.
     *
     * @param mixed $value Data to cache
     *
     * @return int
     *
     * @since  1.0.0
     */
    private function dataType($value) : int
    {
        if (\is_int($value)) {
            return CacheValueType::_INT;
        } elseif (\is_float($value)) {
            return CacheValueType::_FLOAT;
        } elseif (\is_string($value)) {
            return CacheValueType::_STRING;
        } elseif (\is_bool($value)) {
            return CacheValueType::_BOOL;
        } elseif (\is_array($value)) {
            return CacheValueType::_ARRAY;
        } elseif ($value === null) {
            return CacheValueType::_NULL;
        } elseif ($value instanceof \Serializable) {
            return CacheValueType::_SERIALIZABLE;
        } elseif ($value instanceof \JsonSerializable) {
            return CacheValueType::_JSONSERIALIZABLE;
        }

        throw new \InvalidArgumentException('Invalid value');
    }

    /**
     * Create string representation of data for storage
     *
     * @param mixed $value Value of the data
     * @param int   $type  Type of the cache data
     *
     * @return string
     *
     * @throws InvalidEnumValue
     *
     * @since  1.0.0
     */
    private function stringify($value, int $type) : string
    {
        if ($type === CacheValueType::_INT || $type === CacheValueType::_FLOAT || $type === CacheValueType::_STRING || $type === CacheValueType::_BOOL) {
            return (string) $value;
        } elseif ($type === CacheValueType::_ARRAY) {
            return (string) \json_encode($value);
        } elseif ($type === CacheValueType::_SERIALIZABLE) {
            return \get_class($value) . self::DELIM . $value->serialize();
        } elseif ($type === CacheValueType::_JSONSERIALIZABLE) {
            return \get_class($value) . self::DELIM . $value->jsonSerialize();
        } elseif ($type === CacheValueType::_NULL) {
            return '';
        }

        throw new InvalidEnumValue($type);
    }

    /**
     * Get expire offset
     *
     * @param string $raw Raw data
     *
     * @return int
     *
     * @since  1.0.0
     */
    private function getExpire(string $raw) : int
    {
        $expireStart = (int) \strpos($raw, self::DELIM);
        $expireEnd   = (int) \strpos($raw, self::DELIM, $expireStart + 1);

        return (int) \substr($raw, $expireStart + 1, $expireEnd - ($expireStart + 1));
    }

    /**
     * {@inheritdoc}
     */
    public function get($key, int $expire = -1)
    {
        if ($this->status !== CacheStatus::OK) {
            return null;
        }

        $path = $this->getPath($key);
        if (!File::exists($path)) {
            return null;
        }

        $created = Directory::created($path)->getTimestamp();
        $now     = \time();

        if ($expire >= 0 && $created + $expire < $now) {
            return null;
        }

        $raw = \file_get_contents($path);
        if ($raw === false) {
            return null;
        }

        $type        = (int) $raw[0];
        $expireStart = (int) \strpos($raw, self::DELIM);
        $expireEnd   = (int) \strpos($raw, self::DELIM, $expireStart + 1);

        if ($expireStart < 0 || $expireEnd < 0) {
            return null;
        }

        $cacheExpire = \substr($raw, $expireStart + 1, $expireEnd - ($expireStart + 1));
        $cacheExpire = ($cacheExpire === false) ? $created : (int) $cacheExpire;

        if ($cacheExpire >= 0 && $created + $cacheExpire < $now) {
            $this->delete($key);

            return null;
        }

        return $this->reverseValue($type, $raw, $expireEnd);
    }

    /**
     * Parse cached value
     *
     * @param int    $type      Cached value type
     * @param string $raw       Cached value
     * @param int    $expireEnd Value end position
     *
     * @return mixed
     *
     * @since  1.0.0
     */
    private function reverseValue(int $type, string $raw, int $expireEnd)
    {
        $value = null;

        switch ($type) {
            case CacheValueType::_INT:
                $value = (int) \substr($raw, $expireEnd + 1);
                break;
            case CacheValueType::_FLOAT:
                $value = (float) \substr($raw, $expireEnd + 1);
                break;
            case CacheValueType::_BOOL:
                $value = (bool) \substr($raw, $expireEnd + 1);
                break;
            case CacheValueType::_STRING:
                $value = \substr($raw, $expireEnd + 1);
                break;
            case CacheValueType::_ARRAY:
                $array = \substr($raw, $expireEnd + 1);
                $value = \json_decode($array === false ? '[]' : $array, true);
                break;
            case CacheValueType::_NULL:
                $value = null;
                break;
            case CacheValueType::_SERIALIZABLE:
            case CacheValueType::_JSONSERIALIZABLE:
                $namespaceStart = (int) \strpos($raw, self::DELIM, $expireEnd);
                $namespaceEnd   = (int) \strpos($raw, self::DELIM, $namespaceStart + 1);
                $namespace      = \substr($raw, $namespaceStart, $namespaceEnd);

                if ($namespace === false) {
                    return null;
                }

                $value = $namespace::unserialize(\substr($raw, $namespaceEnd + 1));
                break;
        }

        return $value;
    }

    /**
     * {@inheritdoc}
     */
    public function delete($key, int $expire = -1) : bool
    {
        if ($this->status !== CacheStatus::OK) {
            return false;
        }

        $path = $this->getPath($key);
        if ($expire < 0 && File::exists($path)) {
            File::delete($path);

            return true;
        }

        if ($expire >= 0) {
            $created = Directory::created(Directory::sanitize($key, self::SANITIZE))->getTimestamp();
            $now     = \time();
            $raw     = \file_get_contents($path);

            if ($raw === false) {
                return false;
            }

            $expireStart = (int) \strpos($raw, self::DELIM);
            $expireEnd   = (int) \strpos($raw, self::DELIM, $expireStart + 1);

            if ($expireStart < 0 || $expireEnd < 0) {
                return false;
            }

            $cacheExpire = \substr($raw, $expireStart + 1, $expireEnd - ($expireStart + 1));
            $cacheExpire = ($cacheExpire === false) ? $created : (int) $cacheExpire;

            if ($cacheExpire >= 0 && $created + $cacheExpire < $now) {
                $this->delete($key);

                return false;
            }

            if ($cacheExpire >= 0 && $created + $cacheExpire > $now) {
                File::delete($path);

                return true;
            }
        }

        return false;
    }

    /**
     * {@inheritdoc}
     */
    public function flush(int $expire = 0) : bool
    {
        if ($this->status !== CacheStatus::OK) {
            return false;
        }

        $dir = new Directory($this->con);
        $now = \time();

        foreach ($dir as $file) {
            if ($file instanceof File) {
                $created = $file->getCreatedAt()->getTimestamp();
                if (($expire >= 0 && $created + $expire < $now)
                    || ($expire < 0 && $created + $this->getExpire($file->getContent()) < $now)
                ) {
                    File::delete($file->getPath());
                }
            }
        }

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

        $path = $this->getPath($key);

        if (File::exists($path)) {
            File::put($path, $this->build($value, $expire));

            return true;
        }

        return false;
    }

    /**
     * Get cache path
     *
     * @param mixed $key Key for cached value
     *
     * @return string Path to cache file
     *
     * @since  1.0.0
     */
    private function getPath($key) : string
    {
        $path = Directory::sanitize($key, self::SANITIZE);
        return $this->con . '/' . \trim($path, '/') . '.cache';
    }
}
