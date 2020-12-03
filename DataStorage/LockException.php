<?php
/**
 * Orange Management
 *
 * PHP Version 8.0
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
 * Lock exception class.
 *
 * This exception is used for instances that have a lock componenent/state after which rendering,
 * header manipulation etc. are no longer allowed/possible.
 *
 * @package phpOMS\DataStorage
 * @license OMS License 1.0
 * @link    https://orange-management.org
 * @since   1.0.0
 */
final class LockException extends \RuntimeException
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
    public function __construct(string $message, int $code = 0, \Exception $previous = null)
    {
        parent::__construct('Interaction with "' . $message . '" already locked.', $code, $previous);
    }
}
