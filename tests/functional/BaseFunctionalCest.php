<?php

namespace tests\functional;

use \tests\BaseTestCest;
use \tests\helpers\ConsoleRunner;
use \tecsvit\ConsoleLogger;

/**
 * Class BaseConsoleCest
 * @package app\tests\console
 * @author Alexander Mokhonko
 * Date: 2018-01-12
 */
class BaseFunctionalCest extends BaseTestCest
{
    /**
     * @param string        $command
     * @param array|string  $params
     * @return bool
     */
    protected function runConsole($command, $params = null)
    {
        $console = new ConsoleRunner();
        return $console->run($command, $params);
    }

    /**
     * @param string $message
     * @param string $command
     * @return string
     */
    protected function buildError($message, $command)
    {
        $error = sprintf(
            $message,
            PHP_EOL,
            BASE_PATH . '/tests/bin/node ' . $command
        );

        return ConsoleLogger::debugLog($error, true, ConsoleLogger::ERROR);
    }

    /**
     * @param string $message
     * @return string
     */
    protected function buildSuccess($message)
    {
        $error = sprintf(
            $message,
            PHP_EOL,
            BASE_PATH . '/tests/bin/node add'
        );

        return ConsoleLogger::debugLog($error, true);
    }
}
