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
namespace phpOMS\Module;

use phpOMS\DataStorage\Database\DatabaseType;
use phpOMS\DataStorage\Database\DatabasePool;
use phpOMS\System\File\Local\Directory;
use phpOMS\System\File\PathException;
use phpOMS\System\File\PermissionException;
use phpOMS\Utils\Parser\Php\ArrayParser;

/**
 * Installer Abstract class.
 *
 * @category   Framework
 * @package    phpOMS\Module
 * @author     OMS Development Team <dev@oms.com>
 * @author     Dennis Eichhorn <d.eichhorn@oms.com>
 * @license    OMS License 1.0
 * @link       http://orange-management.com
 * @since      1.0.0
 */
class InstallerAbstract
{
    /**
     * Register module in database.
     *
     * @param Pool        $dbPool Database instance
     * @param InfoManager $info   Module info
     *
     * @return void
     *
     * @since  1.0.0
     * @author Dennis Eichhorn
     */
    public static function registerInDatabase(DatabasePool $dbPool, InfoManager $info)
    {
        switch ($dbPool->get()->getType()) {
            case DatabaseType::MYSQL:
                $dbPool->get()->con->beginTransaction();

                $sth = $dbPool->get()->con->prepare(
                    'INSERT INTO `' . $dbPool->get()->prefix . 'module` (`module_id`, `module_theme`, `module_path`, `module_active`, `module_version`) VALUES
                (:internal, :theme, :path, :active, :version);'
                );

                $sth->bindValue(':internal', $info->getInternalName(), \PDO::PARAM_INT);
                $sth->bindValue(':theme', 'Default', \PDO::PARAM_STR);
                $sth->bindValue(':path', $info->getDirectory(), \PDO::PARAM_STR);
                $sth->bindValue(':active', 0, \PDO::PARAM_INT);
                $sth->bindValue(':version', $info->getVersion(), \PDO::PARAM_STR);
                $sth->execute();

                $sth = $dbPool->get()->con->prepare(
                    'INSERT INTO `' . $dbPool->get()->prefix . 'module_load` (`module_load_pid`, `module_load_type`, `module_load_from`, `module_load_for`, `module_load_file`) VALUES
                (:pid, :type, :from, :for, :file);'
                );

                $load = $info->getLoad();
                foreach ($load as $val) {
                    foreach ($val['pid'] as $pid) {
                        $sth->bindValue(':pid', $pid, \PDO::PARAM_STR);
                        $sth->bindValue(':type', $val['type'], \PDO::PARAM_INT);
                        $sth->bindValue(':from', $val['from'], \PDO::PARAM_STR);
                        $sth->bindValue(':for', $val['for'], \PDO::PARAM_STR);
                        $sth->bindValue(':file', $val['file'], \PDO::PARAM_STR);

                        $sth->execute();
                    }
                }

                $dbPool->get()->con->commit();
                break;
        }
    }

    /**
     * Install module.
     *
     * @param string      $routePath Route Path
     * @param Pool        $dbPool    Database instance
     * @param InfoManager $info      Module info
     *
     * @return void
     *
     * @since  1.0.0
     * @author Dennis Eichhorn <d.eichhorn@oms.com>
     */
    public static function install(string $routePath, DatabasePool $dbPool, InfoManager $info)
    {
        self::registerInDatabase($dbPool, $info);
        self::initRoutes($routePath, $info);
        self::activate($dbPool, $info);
    }

    /**
     * Activate after install.
     *
     * @param Pool        $dbPool Database instance
     * @param InfoManager $info   Module info
     *
     * @return void
     *
     * @since  1.0.0
     * @author Dennis Eichhorn <d.eichhorn@oms.com>
     */
    private static function activate(DatabasePool $dbPool, InfoManager $info)
    {
        /** @var ActivateAbstract $class */
        $class = '\Modules\\' . $info->getDirectory() . '\Admin\Activate';
        $class::activate($dbPool, $info);
    }

    /**
     * Re-init module.
     *
     * @param string      $routePath Route Path
     * @param InfoManager $info Module info
     *
     * @return void
     *
     * @since  1.0.0
     * @author Dennis Eichhorn <d.eichhorn@oms.com>
     */
    public static function reInit(string $routePath, InfoManager $info)
    {
        self::initRoutes($routePath, $info);
    }

    /**
     * Init routes.
     *
     * @param string      $routePath Route Path
     * @param InfoManager $info Module info
     *
     * @return void
     *
     * @throws PermissionException
     *
     * @since  1.0.0
     * @author Dennis Eichhorn <d.eichhorn@oms.com>
     */
    private static function initRoutes(string $routePath, InfoManager $info)
    {
        // todo: maybe use static::__DIR__ ?
        $directories = new Directory(ROOT_PATH . '/Modules/' . $info->getDirectory() . '/Admin/Routes');

        foreach ($directories as $key => $subdir) {
            if ($subdir instanceof Directory) {
                foreach ($subdir as $key2 => $file) {
                    self::installRoutes($routePath . '/' . $subdir->getName() . '/' . basename($file->getName(), '.php') . '/Routes.php', $file->getPath());
                }
            }
        }
    }

    /**
     * Install routes.
     *
     * @param string $destRoutePath Destination route path
     * @param string $srcRoutePath  Source route path
     *
     * @return void
     *
     * @throws PermissionException
     *
     * @since  1.0.0
     * @author Dennis Eichhorn <d.eichhorn@oms.com>
     */
    private static function installRoutes(string $destRoutePath, string $srcRoutePath)
    {
        if (!file_exists($destRoutePath)) {
            file_put_contents($destRoutePath, '<?php return [];');
        }

        if (file_exists($destRoutePath) && file_exists($srcRoutePath)) {
            /** @noinspection PhpIncludeInspection */
            $appRoutes = include $destRoutePath;
            /** @noinspection PhpIncludeInspection */
            $moduleRoutes = include $srcRoutePath;

            $appRoutes = array_merge_recursive($appRoutes, $moduleRoutes);

            if (is_writable($destRoutePath)) {
                file_put_contents($destRoutePath, '<?php return' . ArrayParser::serializeArray($appRoutes) . ';', LOCK_EX);
            } else {
                throw new PermissionException($destRoutePath);
            }
        } else {
            if (!file_exists($srcRoutePath)) {
                throw new PathException($srcRoutePath);
            }

            if (!file_exists($destRoutePath)) {
                throw new PathException($destRoutePath);
            }
        }
    }

}
