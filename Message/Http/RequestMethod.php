<?php
/**
 * Orange Management
 *
 * PHP Version 7.0
 *
 * @category   TBD
 * @package    TBD
 * @author     OMS Development Team <dev@oms.com>
 * @author     Dennis Eichhorn <d.eichhorn@oms.com>
 * @copyright  2013 Dennis Eichhorn
 * @license    OMS License 1.0
 * @version    1.0.0
 * @link       http://orange-management.com
 */
namespace phpOMS\Message\Http;

use phpOMS\Datatypes\Enum;

/**
 * Request method enum.
 *
 * @category   Request
 * @package    Framework
 * @author     OMS Development Team <dev@oms.com>
 * @author     Dennis Eichhorn <d.eichhorn@oms.com>
 * @license    OMS License 1.0
 * @link       http://orange-management.com
 * @since      1.0.0
 */
abstract class RequestMethod extends Enum
{
    /* public */ const GET = 'GET';    /* GET */
    /* public */ const POST = 'POST';   /* POST */
    /* public */ const PUT = 'PUT';    /* PUT */
    /* public */ const DELETE = 'DELETE'; /* DELETE */
    /* public */ const HEAD = 'HEAD';   /* HEAD */
    /* public */ const TRACE = 'TRACE';  /* TRACE */
}
