<?php
/**
 * Orange Management
 *
 * PHP Version 8.0
 *
 * @package   phpOMS\Contract
 * @copyright Dennis Eichhorn
 * @license   OMS License 1.0
 * @version   1.0.0
 * @link      https://orange-management.org
 */
declare(strict_types=1);

namespace phpOMS\Contract;

/**
 * This interface forces classes to implement an array representation of themselves.
 *
 * This can be helpful for \JsonSerializable classes or classes which need to be represented as array.
 *
 * @package phpOMS\Contract
 * @license OMS License 1.0
 * @link    https://orange-management.org
 * @since   1.0.0
 */
interface ArrayableInterface
{
    /**
     * Get the instance as an array.
     *
     * @return array
     *
     * @since 1.0.0
     */
    public function toArray() : array;
}
