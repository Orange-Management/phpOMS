<?php
/**
 * Orange Management
 *
 * PHP Version 7.4
 *
 * @package   phpOMS\Utils\IO\Json
 * @copyright Dennis Eichhorn
 * @license   OMS License 1.0
 * @version   1.0.0
 * @link      https://orange-management.org
 */
declare(strict_types=1);

namespace phpOMS\Utils\IO\Json;

/**
 * Cvs interface.
 *
 * @package phpOMS\Utils\IO\Json
 * @license OMS License 1.0
 * @link    https://orange-management.org
 * @since   1.0.0
 */
interface JsonInterface
{
    /**
     * Export Json.
     *
     * @param string $path Path to export
     *
     * @return void
     *
     * @since 1.0.0
     */
    public function exportJson($path) : void;

    /**
     * Import Json.
     *
     * @param string $path Path to import
     *
     * @return void
     *
     * @since 1.0.0
     */
    public function importJson($path) : void;
}
