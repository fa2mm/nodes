<?php
/**
 * @author Olexander Mokhonko <2day@ua.fm>
 * Date: 2018-07-11
 */

spl_autoload_register(function ($class) {

    require_once BASE_PATH . '/vendor/autoload.php';

    $dirs = [
        '/app/models/',
        '/app/core/base/'
    ];

    $parts = explode('\\', $class);
    $className = end($parts);

    foreach ($dirs as $dir) {
        if (file_exists(BASE_PATH . $dir . $className . '.php')) {
            require_once(BASE_PATH . $dir . $className . '.php');
            return;
        }
    }
});
