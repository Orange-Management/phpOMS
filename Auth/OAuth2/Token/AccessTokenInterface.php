<?php
/**
 * Orange Management
 *
 * PHP Version 7.4
 *
 * @package   phpOMS\Auth\OAuth2\Token
 * @copyright Dennis Eichhorn
 * @license   OMS License 1.0
 * @version   1.0.0
 * @link      https://orange-management.org
 * @see       https://tools.ietf.org/html/rfc6749
 */
declare(strict_types=1);

namespace phpOMS\Auth\OAuth2\Token;

/**
 * Provider class.
 *
 * @package phpOMS\Auth\OAuth2\Token
 * @license OMS License 1.0
 * @link    https://orange-management.org
 * @since   1.0.0
 */
interface AccessTokenInterface extends \JsonSerializable
{
    public function getToken() : string;

    public function getRefreshToken() : ?string;

    public function getExpires() : ?int;

    public function hasExpired() : bool;

    public function getValues() : array;

    public function __toString();

    public function jsonSerialize();
}
