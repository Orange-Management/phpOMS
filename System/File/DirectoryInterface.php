<?php
/**
 * Orange Management
 *
 * PHP Version 7.4
 *
 * @package   phpOMS\System\File
 * @copyright Dennis Eichhorn
 * @license   OMS License 1.0
 * @version   1.0.0
 * @link      https://orange-management.org
 */
declare(strict_types=1);

namespace phpOMS\System\File;

/**
 * Filesystem class.
 *
 * Performing operations on the file system
 *
 * @package phpOMS\System\File
 * @license OMS License 1.0
 * @link    https://orange-management.org
 * @since   1.0.0
 *
 * @phpstan-extends \ArrayAccess<string, mixed>
 * @phpstan-extends \Iterator<string, mixed>
 */
interface DirectoryInterface extends \ArrayAccess, \Iterator, ContainerInterface
{
    /**
     * Get node by name.
     *
     * @param string $name File/direcotry name
     *
     * @return null|ContainerInterface
     *
     * @since 1.0.0
     */
    public function getNode(string $name) : ?ContainerInterface;

    /**
     * Add file or directory.
     *
     * @param mixed $file File to add
     *
     * @return bool
     *
     * @since 1.0.0
     */
    public function addNode($file) : bool;
}
