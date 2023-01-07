<?php
/**
 * Karaka
 *
 * PHP Version 8.1
 *
 * @package   phpOMS\Utils\Parser\Spreadsheet
 * @copyright Dennis Eichhorn
 * @license   OMS License 1.0
 * @version   1.0.0
 * @link      https://jingga.app
 */
declare(strict_types=1);

namespace phpOMS\Utils\Parser\Spreadsheet;

use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Worksheet\PageSetup;

/**
 * Spreadsheet parser class.
 *
 * @package phpOMS\Utils\Parser\Spreadsheet
 * @license OMS License 1.0
 * @link    https://jingga.app
 * @since   1.0.0
 */
class SpreadsheetParser
{
    /**
     * Spreadsheet to string
     *
     * @param string $path Path
     *
     * @return string
     *
     * @since 1.0.0
     */
    public static function parseSpreadsheet(string $path, string $output = 'json') : string
    {
        if ($output === 'json') {
            $spreadsheet = IOFactory::load($path);

            $sheetCount = $spreadsheet->getSheetCount();
            for ($i = 0; $i < $sheetCount; ++$i) {
                $csv[] = $spreadsheet->getSheet($i)->toArray(null, true, true, true);
            }

            return \json_encode($csv);
        } elseif ($output === 'pdf') {
            $spreadsheet = IOFactory::load($path);

            $spreadsheet->getActiveSheet()->setShowGridLines(false);
            $spreadsheet->getActiveSheet()->getPageSetup()->setOrientation(PageSetup::ORIENTATION_LANDSCAPE);

            IOFactory::registerWriter('custom', \phpOMS\Utils\Parser\Spreadsheet\SpreadsheetWriter::class);
            $writer = IOFactory::createWriter($spreadsheet, 'custom');

            return $writer->toPdfString();
        } elseif ($output === 'html') {
            $spreadsheet = IOFactory::load($path);

            IOFactory::registerWriter('custom', \phpOMS\Utils\Parser\Spreadsheet\SpreadsheetWriter::class);
            $writer = IOFactory::createWriter($spreadsheet, 'custom');

            return $writer->generateHtmlAll();
        }

        return '';
    }
}
