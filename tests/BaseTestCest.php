<?php

namespace tests;

use \app\core\base\Db;

/**
 * Class BaseTestCest
 * @package tests
 * @author Olexander Mokhonko
 * Date: 2018-07-14
 */
class BaseTestCest
{
    /**
     * @return void
     */
    protected function clearDb()
    {
        Db::getDb()->prepare('TRUNCATE category;')->execute();
    }
}
