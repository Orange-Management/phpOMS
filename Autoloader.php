<?php
/**
 * Karaka
 *
 * PHP Version 8.1
 *
 * @package   phpOMS
 * @copyright Dennis Eichhorn
 * @license   OMS License 1.0
 * @version   1.0.0
 * @link      https://jingga.app
 */
declare(strict_types=1);

namespace phpOMS;

\spl_autoload_register('\phpOMS\Autoloader::defaultAutoloader');

/**
 * Autoloader class.
 *
 * @package phpOMS
 * @license OMS License 1.0
 * @link    https://jingga.app
 * @since   1.0.0
 */
final class Autoloader
{
    /**
     * Base paths for autoloading
     *
     * @var string[]
     * @since 1.0.0
     */
    private static $paths = [
        __DIR__ . '/../',
    ];

    /**
     * Constructor.
     *
     * @since 1.0.0
     * @codeCoverageIgnore
     */
    private function __construct()
    {
    }

    /**
     * Add base path for autoloading
     *
     * @param string $path Absolute base path with / at the end
     *
     * @return void
     *
     * @since 1.0.0
     */
    public static function addPath(string $path) : void
    {
        self::$paths[] = \rtrim($path, '/\\') . '/';
    }

    /**
     * Check if a path is already in the path list
     *
     * @param string $path Absolute base path with / at the end
     *
     * @return bool
     *
     * @since 1.0.0
     */
    public static function inPaths(string $path) : bool
    {
        return \in_array(\rtrim($path, '/\\') . '/', self::$paths);
    }

    /**
     * Find include paths for class
     *
     * @param string $class Class name
     *
     * @return array
     *
     * @since 1.0.0
     */
    public static function findPaths(string $class) : array
    {
        $found = [];
        $class = \ltrim($class, '\\');
        $class = \str_replace(['_', '\\'], '/', $class);

        foreach (self::$paths as $path) {
            if (\is_file($file = $path . $class . '.php')) {
                $found[] = $file;
            }
        }

        return $found;
    }

    /**
     * Loading classes by namespace + class name.
     *
     * @param string $class Class name
     *
     * @example Autoloader::defaultAutoloader('\phpOMS\Autoloader') // void
     *
     * @return void
     *
     * @throws AutoloadException Throws this exception if the class to autoload doesn't exist. This could also be related to a wrong namespace/file path correlation.
     *
     * @since 1.0.0
     */
    public static function defaultAutoloader(string $class) : void
    {
        $class = \ltrim($class, '\\');
        $class = \str_replace(['_', '\\'], '/', $class);

        foreach (self::$paths as $path) {
            if (\is_file($file = $path . $class . '.php')) {
                include $file;

                return;
            }
        }
    }

    /**
     * Check if class exists.
     *
     * @param string $class Class path
     *
     * @example Autoloader::exists('\phpOMS\Autoloader') // true
     *
     * @return bool Returns true if the namespace/class exists, otherwise false
     *
     * @since 1.0.0
     */
    public static function exists(string $class) : bool
    {
        $class = \ltrim($class, '\\');
        $class = \str_replace(['_', '\\'], '/', $class);

        foreach (self::$paths as $path) {
            if (\is_file($path . $class . '.php')) {
                return true;
            }
        }

        return false;
    }

    /**
     * Invalidate a already loaded file
     *
     * IMPORTANT: This does not reload an already loaded file, this is not possible.
     *
     * @param string $class Class to invalidate
     *
     * @return bool
     *
     * @since 1.0.0
     */
    public static function invalidate(string $class) : bool
    {
        if (!\extension_loaded('zend opcache')
            || !\opcache_is_script_cached($class)
            || \opcache_get_status() === false
        ) {
            return false;
        }

        \opcache_invalidate($class);
        \opcache_compile_file($class);

        return true;
    }
}
