<?php
/**
 * Orange Management
 *
 * PHP Version 7.4
 *
 * @package   phpOMS\Utils\Compression
 * @copyright Dennis Eichhorn
 * @license   OMS License 1.0
 * @version   1.0.0
 * @link      https://orange-management.org
 */
declare(strict_types=1);

namespace phpOMS\Utils\Compression;

/**
 * Compression Interface
 *
 * @package phpOMS\Utils\Compression
 * @license OMS License 1.0
 * @link    https://orange-management.org
 * @since   1.0.0
 */
interface CompressionInterface
{
    /**
     * Compresses source text
     *
     * @param string $source Source text to compress
     *
     * @return string
     *
     * @since 1.0.0
     */
    public function compress(string $source) : string;

    /**
     * Decompresses text
     *
     * @param string $compressed Compressed text to decompress
     *
     * @return string
     *
     * @since 1.0.0
     */
    public function decompress(string $compressed) : string;
}
