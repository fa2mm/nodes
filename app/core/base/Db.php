<?php

namespace app\core\base;

use \tecsvit\ObjectHelper as OH;
use \tecsvit\ConsoleLogger as Log;

/**
 * Class Db
 * @author Alexander Mokhonko <2day@ua.fm>
 * Date: 2018-07-11
 * @uses \tecsvit\ObjectHelper
 * @uses \tecsvit\ConsoleLogger
 *
 * @static PDO    $instance
 * @static array  $dbConfig
 */
class Db
{
    private static $instance = null;
    private static $dbConfig = [];

    private function __construct()
    {
    }

    private function __clone()
    {
    }

    /**
     * @param null $dbConfig
     * @return \PDO
     */
    public static function getDb($dbConfig = null)
    {
        if (null === self::$instance) {
            self::initDb($dbConfig);
        }

        return self::$instance;
    }

    /**
     * @param $dbConfig
     * @return void
     */
    public static function initDb($dbConfig)
    {
        if (null === self::$instance && null !== $dbConfig) {
            self::$dbConfig = $dbConfig;
            self::createInstance();
        }
    }

    /**
     * @return void
     */
    private static function createInstance()
    {
        try {
            $dns = OH::getAttribute(self::$dbConfig, 'driver') . ':' .
                'host='     . OH::getAttribute(self::$dbConfig, 'host') . ';' .
                'port='     . OH::getAttribute(self::$dbConfig, 'port') . ';' .
                'dbname='   . OH::getAttribute(self::$dbConfig, 'db') . ';';

            self::$instance = new \PDO(
                $dns,
                OH::getAttribute(self::$dbConfig, 'user'),
                OH::getAttribute(self::$dbConfig, 'pass')
            );
        } catch (\PDOException $e) {
            Log::log('DB Error!: ' . $e->getMessage(), false, Log::ERROR);
            Log::log($e, false, Log::ERROR);
            die(2);
        }
    }
}
