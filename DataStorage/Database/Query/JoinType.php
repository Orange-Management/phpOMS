<?php
/**
 * Orange Management
 *
 * PHP Version 7.1
 *
 * @category   TBD
 * @package    TBD
 * @author     OMS Development Team <dev@oms.com>
 * @author     Dennis Eichhorn <d.eichhorn@oms.com>
 * @copyright  Dennis Eichhorn
 * @license    OMS License 1.0
 * @version    1.0.0
 * @link       http://orange-management.com
 */
namespace phpOMS\DataStorage\Database\Query;

use phpOMS\Datatypes\Enum;

/**
 * Query type enum.
 *
 * @category   Framework
 * @package    phpOMS\DataStorage\Database
 * @author     OMS Development Team <dev@oms.com>
 * @author     Dennis Eichhorn <d.eichhorn@oms.com>
 * @license    OMS License 1.0
 * @link       http://orange-management.com
 * @since      1.0.0
 */
abstract class JoinType extends Enum
{
    /* public */ const JOIN = 'JOIN';
    /* public */ const LEFT_JOIN = 'LEFT JOIN';
    /* public */ const LEFT_OUTER_JOIN = 'LEFT OUTER JOIN';
    /* public */ const LEFT_INNER_JOIN = 'LEFT INNER JOIN';
    /* public */ const RIGHT_JOIN = 'RIGHT JOIN';
    /* public */ const RIGHT_OUTER_JOIN = 'RIGHT OUTER JOIN';
    /* public */ const RIGHT_INNER_JOIN = 'RIGHT INNER JOIN';
    /* public */ const OUTER_JOIN = 'OUTER JOIN';
    /* public */ const INNER_JOIN = 'INNER JOIN';
    /* public */ const CROSS_JOIN = 'CROSS JOIN';
    /* public */ const FULL_OUTER_JOIN = 'FULL OUTER JOIN';
}
