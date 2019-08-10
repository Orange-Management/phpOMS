<?php
/**
 * Orange Management
 *
 * PHP Version 7.2
 *
 * @package    tests
 * @copyright  Dennis Eichhorn
 * @license    OMS License 1.0
 * @version    1.0.0
 * @link       https://orange-management.org
 */
 declare(strict_types=1);

namespace phpOMS\tests\Algorithm\Sort;

use phpOMS\Algorithm\Sort\SortableInterface;
use phpOMS\Algorithm\Sort\SortOrder;

require_once __DIR__ . '/../../Autoloader.php';

class NumericElement implements SortableInterface
{
    public $value = 0;

    public function __construct($value) 
    {
        $this->value = $value;
    }

    public function compare(SortableInterface $obj, int $order = SortOrder::ASC) : bool
    {
        return $order === SortOrder::ASC ? $this->value > $obj->value : $this->value < $obj->value;
    }
}