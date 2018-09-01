<?php
/**
 * Orange Management
 *
 * PHP Version 7.2
 *
 * @package    phpOMS\Utils
 * @copyright  Dennis Eichhorn
 * @license    OMS License 1.0
 * @version    1.0.0
 * @link       http://website.orange-management.de
 */
declare(strict_types=1);

namespace phpOMS\Utils;

/**
 * Test utils.
 *
 * Only for testing purposes. MUST NOT be used for other purposes.
 *
 * @package    phpOMS\Utils
 * @license    OMS License 1.0
 * @link       http://website.orange-management.de
 * @since      1.0.0
 */
final class TestUtils
{
    /**
     * Constructor.
     *
     * @since  1.0.0
     * @codeCoverageIgnore
     */
    private function __construct()
    {

    }

    /**
     * Set private object member
     *
     * @param object|string $obj   Object to modify
     * @param string        $name  Member name to modify
     * @param mixed         $value Value to set
     *
     * @return bool The function returns true after setting the member
     *
     * @since  1.0.0
     */
    public static function setMember($obj, string $name, $value) : bool
    {
        $reflectionClass = new \ReflectionClass(\is_string($obj) ? $obj : \get_class($obj));

        if (!$reflectionClass->hasProperty($name)) {
            return false;
        }

        $reflectionProperty = $reflectionClass->getProperty($name);

        if (!($accessible = $reflectionProperty->isPublic())) {
            $reflectionProperty->setAccessible(true);
        }

        if (\is_string($obj)) {
            $reflectionProperty->setValue($value);
        } elseif (\is_object($obj)) {
            $reflectionProperty->setValue($obj, $value);
        }

        if (!$accessible) {
            $reflectionProperty->setAccessible(false);
        }

        return true;
    }

    /**
     * Get private object member
     *
     * @param object $obj  Object to read
     * @param string $name Member name to read
     *
     * @return mixed Returns the member variable value
     *
     * @since  1.0.0
     */
    public static function getMember(object $obj, string $name)
    {
        $reflectionClass = new \ReflectionClass(\is_string($obj) ? $obj : \get_class($obj));

        if (!$reflectionClass->hasProperty($name)) {
            return null;
        }

        $reflectionProperty = $reflectionClass->getProperty($name);

        if (!($accessible = $reflectionProperty->isPublic())) {
            $reflectionProperty->setAccessible(true);
        }

        $value = $reflectionProperty->getValue($obj);

        if (!$accessible) {
            $reflectionProperty->setAccessible(false);
        }

        return $value;
    }
}
