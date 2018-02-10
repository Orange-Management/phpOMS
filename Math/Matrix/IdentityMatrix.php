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

namespace phpOMS\Math\Matrix;

/**
 * Matrix class
 *
 * @package    Framework
 * @license    OMS License 1.0
 * @link       http://website.orange-management.de
 * @since      1.0.0
 */
class IdentityMatrix extends Matrix
{
    /**
     * Constructor.
     *
     * @param int $n Matrix dimension
     *
     * @since  1.0.0
     */
    public function __construct(int $n)
    {
        parent::__construct($n, $n);

        for ($i = 0; $i < $n; ++$i) {
            $this->matrix[$i][$i] = 1;
        }
    }
}
