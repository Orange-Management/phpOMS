<?php
/**
 * Karaka
 *
 * PHP Version 8.0
 *
 * @package   phpOMS\DataStorage\Database\Connection
 * @copyright Dennis Eichhorn
 * @license   OMS License 1.0
 * @version   1.0.0
 * @link      https://karaka.app
 */
declare(strict_types=1);

namespace phpOMS\DataStorage\Database\Connection;

/**
 * Database handler.
 *
 * @package phpOMS\DataStorage\Database\Connection
 * @license OMS License 1.0
 * @link    https://karaka.app
 * @since   1.0.0
 */
final class NullConnection extends ConnectionAbstract
{
    /**
     * {@inheritdoc}
     */
    public function connect(array $dbdata = null) : void
    {
    }
}
