<?php

namespace tests\functional\node;

use \tests\functional\BaseFunctionalCest;
use \app\core\base\Db;

/**
 * Class AddParentCest
 * @package tests\functional\node
 * @author Olexander Mokhonko <2day@ua.fm>
 * Date: 2018-07-15
 */
class AddParentCest extends BaseFunctionalCest
{
    /**
     * @return void
     */
    public function _before()
    {
        $this->clearDb();
        Db::getDb()->prepare('INSERT category SET id = 1, title = "first", lft = 1, rgt = 2, lvl = 1')->execute();
    }

    /**
     * @param \FunctionalTester $I
     * @return void
     */
    public function successWithParentId(\FunctionalTester $I)
    {
        $I->seeInDatabase('category', ['id' => 1, 'title' => 'first', 'lft' => 1, 'rgt' => 2, 'lvl' => 1]);
        $I->dontSeeInDatabase('category', ['id' => 2, 'title' => 'newNodeName', 'lft' => 2, 'rgt' => 3, 'lvl' => 2]);

        $answer = $this->buildSuccess('Node "newNodeName" has been added with id #2');
        expect($this->runConsole('node', ['add', 'newNodeName', 1]))->equals($answer);

        $I->seeInDatabase('category', ['id' => 1, 'title' => 'first', 'lft' => 1, 'rgt' => 4, 'lvl' => 1]);
        $I->seeInDatabase('category', ['id' => 2, 'title' => 'newNodeName', 'lft' => 2, 'rgt' => 3, 'lvl' => 2]);
    }

    /**
     * @return void
     */
    public function failParentId()
    {
        $answer = $this->buildError('Node Parent ID must be integer', 'add');
        expect($this->runConsole('node', ['add', 'nodeName', 'failParentId']))->equals($answer);
    }
}
