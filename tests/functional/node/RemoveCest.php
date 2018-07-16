<?php

namespace tests\functional\node;

use \tests\functional\BaseFunctionalCest;
use \app\core\base\Db;

/**
 * Class RemoveCest
 * @package tests\functional\node
 * @author Olexander Mokhonko <2day@ua.fm>
 * Date: 2018-07-15
 */
class RemoveCest extends BaseFunctionalCest
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
    public function removeOne(\FunctionalTester $I)
    {
        $answer = $this->buildSuccess('Node ID #5 has been deleted');
        expect($this->runConsole('node', ['remove', 5]))->equals($answer);

        $I->dontSeeInDatabase('category', ['id' => 5]);
        $I->seeInDatabase('category', ['id' => 1, 'lft' => 1, 'rgt' => 8, 'lvl' => 1]);
        $I->seeInDatabase('category', ['id' => 2, 'lft' => 2, 'rgt' => 7, 'lvl' => 2]);
        $I->seeInDatabase('category', ['id' => 3, 'lft' => 3, 'rgt' => 6, 'lvl' => 3]);
        $I->seeInDatabase('category', ['id' => 4, 'lft' => 4, 'rgt' => 5, 'lvl' => 4]);
    }

    /**
     * @param \FunctionalTester $I
     * @return void
     */
    public function removeAll(\FunctionalTester $I)
    {
        $answer = $this->buildSuccess('Node ID #2 has been deleted');
        expect($this->runConsole('node', ['remove', 2]))->equals($answer);

        $I->dontSeeInDatabase('category', ['id' => 5]);
        $I->dontSeeInDatabase('category', ['id' => 4]);
        $I->dontSeeInDatabase('category', ['id' => 3]);
        $I->dontSeeInDatabase('category', ['id' => 2]);
        $I->seeInDatabase('category', ['id' => 1, 'lft' => 1, 'rgt' => 2, 'lvl' => 1]);
    }

    /**
     * @return void
     */
    public function fail()
    {
        $answer = $this->buildError('Node ID cannot be blank. Example: %s %s [NODE_ID:mandatory]', 'remove');
        expect($this->runConsole('node', ['remove']))->equals($answer);
    }
}
