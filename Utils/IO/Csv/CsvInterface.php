<?php
/**
 * Orange Management
 *
 * PHP Version 7.4
 *
 * @package   phpOMS\Utils\IO\Csv
 * @copyright Dennis Eichhorn
 * @license   OMS License 1.0
 * @version   1.0.0
 * @link      https://orange-management.org
 */
declare(strict_types=1);

namespace phpOMS\Utils\IO\Csv;

/**
 * Cvs interface.
 *
 * @package    phpOMS\Utils\IO\Csv
 * @license OMS License 1.0
 * @link    https://orange-management.org
 * @since   1.0.0
 */
interface CsvInterface
{
    /**
     * Export Csv.
     *
     * @param string $path Path to export
     *
     * @since  1.0.0
     * @author Dennis Eichhorn <d.eichhorn@oms.com>
     */
    public function exportCsv($path);

    /**
     * Import Csv.
     *
     * @param string $path Path to import
     *
     * @return void
     *
     * @since  1.0.0
     * @author Dennis Eichhorn <d.eichhorn@oms.com>
     */
    public function importCsv($path) : void;
}
