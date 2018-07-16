<?php

namespace tests\functional\node;

use \tests\functional\BaseFunctionalCest;

/**
 * Class AddCest
 * @package tests\functional\node
 * @author Olexander Mokhonko <2day@ua.fm>
 * Date: 2018-07-15
 */
class AddCest extends BaseFunctionalCest
{
    /**
     * @return void
     */
    public function _before()
    {
        $this->clearDb();
    }

    /**
     * @return void
     */
    public function successWithoutParentId()
    {
        $answer = $this->buildSuccess('Node "newNodeName" has been added with id #1');
        expect($this->runConsole('node', ['add', 'newNodeName']))->equals($answer);
    }

    /**
     * @return void
     */
    public function failEmptyNodeTitle()
    {
        $answer = $this->buildError(
            'Node Title cannot be blank. Example: %s %s [NODE_TITLE:mandatory] [NODE_PARENT_ID:optional]',
            'add'
        );

        expect($this->runConsole('node', ['add']))->equals($answer);
    }
}
