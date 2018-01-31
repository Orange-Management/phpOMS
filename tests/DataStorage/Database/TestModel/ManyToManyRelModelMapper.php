<?php
/**
 * Orange Management
 *
 * PHP Version 7.1
 *
 * @package    TBD
 * @copyright  Dennis Eichhorn
 * @license    OMS License 1.0
 * @version    1.0.0
 * @link       http://website.orange-management.de
 */
declare(strict_types = 1);
namespace phpOMS\tests\DataStorage\Database\TestModel;

use phpOMS\DataStorage\Database\DataMapperAbstract;
use phpOMS\DataStorage\Database\Query\Builder;
use phpOMS\DataStorage\Database\Query\Column;
use phpOMS\DataStorage\Database\RelationType;

class ManyToManyRelModelMapper extends DataMapperAbstract
{

    /**
     * Columns.
     *
     * @var array
     * @since 1.0.0
     */
    protected static $columns = [
        'test_has_many_rel_id'          => ['name' => 'test_has_many_rel_id', 'type' => 'int', 'internal' => 'id'],
        'test_has_many_rel_string'        => ['name' => 'test_has_many_rel_string', 'type' => 'string', 'internal' => 'string'],
    ];

    protected static $table = 'test_has_many_rel';

    protected static $primaryField = 'test_has_many_rel_id';
}