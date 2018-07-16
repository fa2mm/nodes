<?php

namespace tests\functional\node;

use \tests\functional\BaseFunctionalCest;
use \app\core\base\Db;

/**
 * Class RenameCest
 * @package tests\functional\node
 * @author Olexander Mokhonko <2day@ua.fm>
 * Date: 2018-07-15
 */
class RenameCest extends BaseFunctionalCest
{
    /**
     * @return void
     */
    public function _before()
    {
        $this->clearDb();
        Db::getDb()->prepare('INSERT category SET id = 1, title = "first", lft = 1, rgt = 10, lvl = 1')->execute();
        Db::getDb()->prepare('INSERT category SET id = 2, title = "second", lft = 2, rgt = 9, lvl = 2')->execute();
        Db::getDb()->prepare('INSERT category SET id = 3, title = "third", lft = 3, rgt = 8, lvl = 3')->execute();
        Db::getDb()->prepare('INSERT category SET id = 4, title = "fourth", lft = 4, rgt = 5, lvl = 4')->execute();
        Db::getDb()->prepare('INSERT category SET id = 5, title = "fifth", lft = 6, rgt = 7, lvl = 4')->execute();
    }

    /**
     * @param \FunctionalTester $I
     * @return void
     */
    public function deleteOne(\FunctionalTester $I)
    {
        $answer = $this->buildSuccess('Node ID #3 has been renamed');
        expect($this->runConsole('node', ['rename', 3, 'newTitle']))->equals($answer);

        $I->seeInDatabase('category', ['id' => 1, 'title' => 'first']);
        $I->seeInDatabase('category', ['id' => 2, 'title' => 'second']);
        $I->seeInDatabase('category', ['id' => 3, 'title' => 'newTitle']);
        $I->seeInDatabase('category', ['id' => 4, 'title' => 'fourth']);
        $I->seeInDatabase('category', ['id' => 5, 'title' => 'fifth']);
    }
}
