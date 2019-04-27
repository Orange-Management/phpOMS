<?php
/**
 * Orange Management
 *
 * PHP Version 7.2
 *
 * @package    phpOMS\Utils\IO\Csv
 * @copyright  Dennis Eichhorn
 * @license    OMS License 1.0
 * @version    1.0.0
 * @link       http://website.orange-management.de
 */
declare(strict_types=1);

namespace phpOMS\Utils\IO\Csv;

/**
 * Options trait.
 *
 * @package    phpOMS\Utils\IO\Csv
 * @since      1.0.0
 */
class CsvSettings
{
    /**
     * Get csv file delimiter.
     *
     * @param mixed    $file       File resource
     * @param int      $checkLines Lines to check for evaluation
     * @param string[] $delimiters Potential delimiters
     *
     * @return string
     *
     * @since  1.0.0
     */
    public static function getFileDelimiter($file, int $checkLines = 2, array $delimiters = [',', '\t', ';', '|', ':']) : string
    {
        $results = [];
        $i       = 0;
        $line    = \fgets($file);

        if ($line === false) {
            return ';';
        }

        while ($line !== false && $i < $checkLines) {
            ++$i;

            foreach ($delimiters as $delimiter) {
                $regExp = '/[' . $delimiter . ']/';
                $fields = \preg_split($regExp, $line);

                if ($fields === false) {
                    return ';';
                }

                if (\count($fields) > 1) {
                    if (!empty($results[$delimiter])) {
                        ++$results[$delimiter];
                    } else {
                        $results[$delimiter] = 1;
                    }
                }
            }
        }

        $results = \array_keys($results, \max($results));

        return $results[0];
    }
}
