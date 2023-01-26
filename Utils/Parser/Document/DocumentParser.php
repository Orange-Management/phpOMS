<?php
/**
 * Karaka
 *
 * PHP Version 8.1
 *
 * @package   phpOMS\Utils\Parser\Document
 * @copyright Dennis Eichhorn
 * @license   OMS License 1.0
 * @version   1.0.0
 * @link      https://jingga.app
 */
declare(strict_types=1);

namespace phpOMS\Utils\Parser\Document;

use PhpOffice\PhpWord\IOFactory;
use PhpOffice\PhpWord\Writer\HTML;

/**
 * Presentation parser class.
 *
 * @package phpOMS\Utils\Parser\Document
 * @license OMS License 1.0
 * @link    https://jingga.app
 * @since   1.0.0
 */
class DocumentParser
{
    /**
     * Document to string
     *
     * @param string $path Path
     *
     * @return string
     *
     * @since 1.0.0
     */
    public static function parseDocument(string $path, string $output = 'html') : string
    {
        if ($output === 'html') {
            $doc = IOFactory::load($path);

            $writer = new HTML($doc);

            return $writer->getContent();
        } elseif ($output === 'pdf') {
            $doc = IOFactory::load($path);

            $writer = new DocumentWriter($doc);

            return $writer->toPdfString();
        }

        return '';
    }
}