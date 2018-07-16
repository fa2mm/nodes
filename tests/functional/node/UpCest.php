<?php

namespace tests\functional\node;

use \tests\functional\BaseFunctionalCest;
use \app\core\base\Db;

/**
 * Class UpCest
 * @package tests\functional\node
 * @author Olexander Mokhonko <2day@ua.fm>
 * Date: 2018-07-15
 */
class UpCest extends BaseFunctionalCest
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
    public function up(\FunctionalTester $I)
    {
        $answer = $this->buildSuccess('Node ID #5 has been moved at the top');
        expect($this->runConsole('node', ['up', 5]))->equals($answer);

        $I->seeInDatabase('category', ['id' => 5, 'title' => 'fifth', 'lft' => 3, 'rgt' => 8, 'lvl' => 3]);
        $I->seeInDatabase('category', ['id' => 3, 'title' => 'third', 'lft' => 6, 'rgt' => 7, 'lvl' => 4]);
    }

    /**
     * @param \FunctionalTester $I
     * @return void
     */
    public function up2(\FunctionalTester $I)
    {
        $answer = $this->buildSuccess('Node ID #2 has been moved at the top');
        expect($this->runConsole('node', ['up', 2]))->equals($answer);

        $I->seeInDatabase('category', ['id' => 2, 'title' => 'second', 'lft' => 1, 'rgt' => 10, 'lvl' => 1]);
        $I->seeInDatabase('category', ['id' => 1, 'title' => 'first', 'lft' => 2, 'rgt' => 9, 'lvl' => 2]);
    }

    /**
     * @param \FunctionalTester $I
     * @return void
     */
    public function failUp(\FunctionalTester $I)
    {
        $answer = $this->buildError('Error, node with ID #1 already at the top (does not have parent)', 'up');
        expect($this->runConsole('node', ['up', 1]))->equals($answer);

        $I->seeInDatabase('category', ['id' => 1, 'title' => 'first', 'lft' => 1, 'rgt' => 10, 'lvl' => 1]);
    }
}
