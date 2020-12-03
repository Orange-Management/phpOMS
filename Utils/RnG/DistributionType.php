<?php
/**
 * Orange Management
 *
 * PHP Version 8.0
 *
 * @package   phpOMS\Utils\RnG
 * @copyright Dennis Eichhorn
 * @license   OMS License 1.0
 * @version   1.0.0
 * @link      https://orange-management.org
 */
declare(strict_types=1);

namespace phpOMS\Utils\RnG;

use phpOMS\Stdlib\Base\Enum;

/**
 * Distribution type enum.
 *
 * @package phpOMS\Utils\RnG
 * @license OMS License 1.0
 * @link    https://orange-management.org
 * @since   1.0.0
 */
abstract class DistributionType extends Enum
{
    public const UNIFORM = 0;

    public const NORMAL = 1;
}
