<?php
/**
 * Orange Management
 *
 * PHP Version 7.4
 *
 * @package   phpOMS\DataStorage\Database\Exception
 * @copyright Dennis Eichhorn
 * @license   OMS License 1.0
 * @version   1.0.0
 * @link      https://orange-management.org
 */
declare(strict_types=1);

namespace phpOMS\DataStorage\Database\Exception;

/**
 * Permission exception class.
 *
 * @package phpOMS\DataStorage\Database\Exception
 * @license OMS License 1.0
 * @link    https://orange-management.org
 * @since   1.0.0
 */
final class InvalidDatabaseTypeException extends \InvalidArgumentException
{
    /**
     * Constructor.
     *
     * @param string     $message  Exception message
     * @param int        $code     Exception code
     * @param \Exception $previous Previous exception
     *
     * @since 1.0.0
     */
    public function __construct(string $message = '', int $code = 0, \Exception $previous = null)
    {
        parent::__construct('Invalid database type "' . $message . '".', $code, $previous);
    }
}
