<?php
/**
 * Karaka
 *
 * PHP Version 8.0
 *
 * @package   phpOMS\Validation
 * @copyright Dennis Eichhorn
 * @license   OMS License 1.0
 * @version   1.0.0
 * @link      https://karaka.app
 */
declare(strict_types=1);

namespace phpOMS\Validation;

use phpOMS\Utils\StringUtils;

/**
 * Validator class.
 *
 * @package phpOMS\Validation
 * @license OMS License 1.0
 * @link    https://karaka.app
 * @since   1.0.0
 */
final class Validator extends ValidatorAbstract
{
    /**
     * Validate variable based on multiple factors.
     *
     * @param mixed $var         Variable to validate
     * @param array $constraints Constraints for validation
     *
     * @return bool
     *
     * @throws \BadFunctionCallException this exception is thrown if the callback is not callable
     *
     * @since 1.0.0
     */
    public static function isValid(mixed $var, array $constraints = null) : bool
    {
        if ($constraints === null) {
            return true;
        }

        foreach ($constraints as $test => $settings) {
            $callback = StringUtils::endsWith($test, 'Not') ? \substr($test, 0, -3) : (string) $test;

            if (!\is_callable($callback)) {
                throw new \BadFunctionCallException();
            }

            $valid = !empty($settings) ? $callback($var, ...$settings) : $callback($var);
            $valid = (StringUtils::endsWith($test, 'Not') ? !$valid : $valid);

            if (!$valid) {
                return false;
            }
        }

        return true;
    }

    /**
     * Validate variable by type.
     *
     * @param mixed           $var        Variable to validate
     * @param string|string[] $constraint Array of allowed types
     *
     * @return bool
     *
     * @since 1.0.0
     */
    public static function isType(mixed $var, string | array $constraint) : bool
    {
        if (!\is_array($constraint)) {
            $constraint = [$constraint];
        }

        foreach ($constraint as $key => $value) {
            if (!\is_a($var, $value)) {
                return false;
            }
        }

        return true;
    }

    /**
     * Validate variable by length.
     *
     * @param string $var Variable to validate
     * @param int    $min Min. length
     * @param int    $max Max. length
     *
     * @return bool
     *
     * @since 1.0.0
     */
    public static function hasLength(string $var, int $min = 0, int $max = \PHP_INT_MAX) : bool
    {
        $length = \strlen($var);

        if ($length <= $max && $length >= $min) {
            return true;
        }

        return false;
    }

    /**
     * Validate variable by substring.
     *
     * @param string       $var    Variable to validate
     * @param array|string $substr Substring
     *
     * @return bool
     *
     * @since 1.0.0
     */
    public static function contains(string $var, string | array $substr) : bool
    {
        return \is_string($substr) ? \strpos($var, $substr) !== false : StringUtils::contains($var, $substr);
    }

    /**
     * Validate variable by pattern.
     *
     * @param string $var     Variable to validate
     * @param string $pattern Pattern for validation
     *
     * @return bool
     *
     * @since 1.0.0
     */
    public static function matches(string $var, string $pattern) : bool
    {
        return (\preg_match($pattern, $var) === 1 ? true : false);
    }

    /**
     * Validate variable by interval.
     *
     * @param int|float $var Variable to validate
     * @param int|float $min Min. value
     * @param int|float $max Max. value
     *
     * @return bool
     *
     * @since 1.0.0
     */
    public static function hasLimit(int | float $var, int | float $min = 0, int | float $max = \PHP_INT_MAX) : bool
    {
        if ($var <= $max && $var >= $min) {
            return true;
        }

        return false;
    }
}
