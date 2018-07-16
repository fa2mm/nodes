<?php

namespace tests\helpers;

/**
 * Class ConsoleRunner
 * @package tests\helpers
 *
 * @property string $basePath
 *
 * Date: 2018-07-13
 */
class ConsoleRunner
{
    private $basePath;

    /**
     * Running console command on background
     *
     * @param string    $cmd argument that passed to console application
     * @param array     $params
     * @param boolean   $devNull
     * @return int|string
     */
    public function run($cmd, $params = null, $devNull = false)
    {
        $cmd = $this->getBasePath() . DIRECTORY_SEPARATOR . $cmd;

        if (is_array($params)) {
            $cmd .= ' ' . implode(' ', $params);
        } elseif (is_numeric($params) || is_string($params)) {
            $cmd .= ' ' . $params;
        }

        return $this->execute($cmd, $devNull);
    }

    /**
     * @param string    $cmd
     * @param boolean   $devNull
     * @return int|string
     */
    public function execute($cmd, $devNull = false)
    {
        if ($this->isWindows()) {
            return pclose(popen('start /b ' . $cmd, 'r'));
        } else {
            $cmd .= $devNull ? ' > /dev/null 2>&1 &' : '';
            return shell_exec($cmd);
        }
    }

    /**
     * @param string $basePath
     * @return void
     */
    public function setBasePath($basePath)
    {
        $this->basePath = $basePath;
    }

    /**
     * Function to check operating system
     *
     * @return boolean
     */
    protected function isWindows()
    {
        if (PHP_OS == 'WINNT' || PHP_OS == 'WIN32') {
            return true;
        } else {
            return false;
        }
    }

    /**
     * @return string
     */
    private function getBasePath()
    {
        if ($this->basePath === null) {
            return dirname(__DIR__) . DIRECTORY_SEPARATOR . 'bin';
        }

        return $this->basePath;
    }
}
