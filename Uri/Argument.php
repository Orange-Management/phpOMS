<?php
/**
 * Orange Management
 *
 * PHP Version 7.2
 *
 * @package    phpOMS\Uri
 * @copyright  Dennis Eichhorn
 * @license    OMS License 1.0
 * @version    1.0.0
 * @link       http://website.orange-management.de
 */
declare(strict_types=1);

namespace phpOMS\Uri;

use phpOMS\Utils\StringUtils;

/**
 * Uri interface.
 *
 * Used in order to create and evaluate a uri
 *
 * @package    phpOMS\Uri
 * @license    OMS License 1.0
 * @link       http://website.orange-management.de
 * @since      1.0.0
 *
 * @SuppressWarnings(PHPMD.Superglobals)
 */
final class Argument implements UriInterface
{
    
    /**
     * Root path.
     *
     * @var string
     * @since 1.0.0
     */
    private $rootPath = '/';

    /**
     * Uri.
     *
     * @var string
     * @since 1.0.0
     */
    private $uri = '';

    /**
     * Uri scheme.
     *
     * @var string
     * @since 1.0.0
     */
    private $scheme = '';

    /**
     * Uri host.
     *
     * @var string
     * @since 1.0.0
     */
    private $host = '';

    /**
     * Uri port.
     *
     * @var int
     * @since 1.0.0
     */
    private $port = 80;

    /**
     * Uri user.
     *
     * @var string
     * @since 1.0.0
     */
    private $user = '';

    /**
     * Uri password.
     *
     * @var string
     * @since 1.0.0
     */
    private $pass = '';

    /**
     * Uri path.
     *
     * @var string
     * @since 1.0.0
     */
    private $path = '';

    /**
     * Uri query.
     *
     * @var array
     * @since 1.0.0
     */
    private $query = [];

    /**
     * Uri query.
     *
     * @var string
     * @since 1.0.0
     */
    private $queryString = '';

    /**
     * Uri fragment.
     *
     * @var string
     * @since 1.0.0
     */
    private $fragment = '';

    /**
     * Uri base.
     *
     * @var string
     * @since 1.0.0
     */
    private $base = '';

    /**
     * Constructor.
     *
     * @param string $uri Root path for subdirectory
     *
     * @since  1.0.0
     */
    public function __construct(string $uri)
    {
        $this->set($uri);
    }

    /**
     * {@inheritdoc}
     */
    public function set(string $uri) : void
    {
        $this->uri = $uri;

        $temp       = $this->__toString();
        $found      = \stripos($temp, ':');
        $path       = $found !== false && $found > 3 && $found < 8 ? \substr($temp, $found) : $temp;
        $this->path = $path === false ? '' : $path;
    }

    /**
     * {@inheritdoc}
     */
    public static function isValid(string $uri) : bool
    {
        return true;
    }

    /**
     * {@inheritdoc}
     */
    public function getRootPath() : string
    {
        return $this->rootPath;
    }

    /**
     * {@inheritdoc}
     */
    public function setRootPath(string $root) : void
    {
        $this->rootPath = $root;
        $this->set($this->uri);
    }

    /**
     * {@inheritdoc}
     */
    public function getScheme() : string
    {
        return $this->scheme;
    }

    /**
     * {@inheritdoc}
     */
    public function getHost() : string
    {
        return $this->host;
    }

    /**
     * {@inheritdoc}
     */
    public function getPort() : int
    {
        return $this->port;
    }

    /**
     * {@inheritdoc}
     */
    public function getPass() : string
    {
        return $this->pass;
    }

    /**
     * {@inheritdoc}
     */
    public function getPath() : string
    {
        return $this->path;
    }

    /**
     * Get path offset.
     *
     * @return int
     *
     * @since  1.0.0
     */
    public function getPathOffset() : int
    {
        return \substr_count($this->rootPath, '/') - 1;
    }

    /**
     * {@inheritdoc}
     */
    public function getRoute() : string
    {
        $query = $this->getQuery();
        return $this->path . (!empty($query) ? '?' . $this->getQuery() : '');
    }

    /**
     * {@inheritdoc}
     */
    public function getQuery(string $key = null)  : string
    {
        if ($key !== null) {
            $key = \strtolower($key);

            return $this->query[$key] ?? '';
        }

        return $this->queryString;
    }

    /**
     * {@inheritdoc}
     */
    public function getPathElement(int $pos = null) : string
    {
        return \explode('/', $this->path)[$pos] ?? '';
    }

    /**
     * {@inheritdoc}
     */
    public function getPathElements() : array
    {
        return \explode('/', $this->path);
    }

    /**
     * {@inheritdoc}
     */
    public function getQueryArray() : array
    {
        return $this->query;
    }

    /**
     * {@inheritdoc}
     */
    public function getFragment() : string
    {
        return $this->fragment;
    }

    /**
     * {@inheritdoc}
     */
    public function getBase() : string
    {
        return $this->base;
    }

    /**
     * {@inheritdoc}
     */
    public function __toString()
    {
        return $this->uri;
    }

    /**
     * {@inheritdoc}
     */
    public function getAuthority() : string
    {
        return '';
    }

    /**
     * {@inheritdoc}
     */
    public function getUser() : string
    {
        return $this->user;
    }

    /**
     * {@inheritdoc}
     */
    public function getUserInfo() : string
    {
        return '';
    }
}