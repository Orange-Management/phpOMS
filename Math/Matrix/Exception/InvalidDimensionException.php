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

namespace phpOMS\Math\Matrix\Exception;

/**
 * Zero devision exception.
 *
 * @package    Framework
 * @license    OMS License 1.0
 * @link       http://website.orange-management.de
 * @since      1.0.0
 */
class InvalidDimensionException extends \UnexpectedValueException
{
    /**
     * Constructor.
     *
     * @param mixed      $message  Exception message
     * @param int        $code     Exception code
     * @param \Exception $previous Previous exception
     *
     * @since  1.0.0
     */
    public function __construct($message, int $code = 0, \Exception $previous = null)
    {
        parent::__construct('Dimension "' . $message . '" is not valid.', $code, $previous);
    }
}
