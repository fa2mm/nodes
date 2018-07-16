<?php

namespace app\models;

use \tecsvit\ObjectHelper as OH;

/**
 * Class NodeAdapter
 * @package app\models
 * @author Olexander Mokhonko <2day@ua.fm>
 * Date: 2018-07-13
 */
class NodeAdapter extends Node
{
    /**
     * Node constructor.
     * @param array $config
     * @param bool  $verbose
     */
    public function __construct(array $config = [], $verbose = false)
    {
        parent::__construct($config, $verbose);
    }

    /**
     * @param array $arguments
     * @return bool
     * @throws \Exception
     */
    public function addAdapter($arguments)
    {
        $title      = OH::getAttribute($arguments, 0);
        $parentId   = OH::getAttribute($arguments, 1);

        if (empty($title)) {
            $error = sprintf(
                'Node Title cannot be blank. Example: %s %s [NODE_TITLE:mandatory] [NODE_PARENT_ID:optional]',
                PHP_EOL,
                $this->command
            );
            $this->addError($error);

            return false;
        } elseif (!empty($parentId) && !is_numeric($parentId) && !is_integer($parentId)) {
            $this->addError('Node Parent ID must be integer');
            return false;
        }

        return parent::add($title, $parentId);
    }

    /**
     * @param array $arguments
     * @return bool
     * @throws \Exception
     */
    public function removeAdapter($arguments)
    {
        $id = OH::getAttribute($arguments, 0);

        if (empty($id)) {
            $error = sprintf('Node ID cannot be blank. Example: %s %s [NODE_ID:mandatory]', PHP_EOL, $this->command);
            $this->addError($error);

            return false;
        } elseif (!is_numeric($id) && !is_integer($id)) {
            $this->addError('Node ID must be integer');
            return false;
        }

        return parent::remove($id);
    }

    /**
     * @param array $arguments
     * @return bool
     * @throws \Exception
     */
    public function renameAdapter($arguments)
    {
        $id         = OH::getAttribute($arguments, 0);
        $newTitle   = OH::getAttribute($arguments, 1);

        if (empty($id) || empty($newTitle)) {
            $error = sprintf(
                'Node ID and New Title cannot be blank. Example: %s %s [NODE_ID:mandatory] [NEW_NODE_TITLE:mandatory]',
                PHP_EOL,
                $this->command
            );
            $this->addError($error);

            return false;
        } elseif (!is_numeric($id) && !is_integer($id)) {
            $this->addError('Node ID must be integer');
            return false;
        }

        return parent::rename($id, $newTitle);
    }

    /**
     * @param array $arguments
     * @return bool
     * @throws \Exception
     */
    public function upAdapter($arguments)
    {
        $id = $this->move($arguments);
        return parent::up($id);
    }

    /**
     * @param array $arguments
     * @return bool
     * @throws \Exception
     */
    public function downAdapter($arguments)
    {
        $id = $this->move($arguments);
        return parent::down($id);
    }

    /**
     * @param array $arguments
     * @return bool|mixed
     */
    private function move($arguments)
    {
        $id = OH::getAttribute($arguments, 0);

        if (empty($id)) {
            $error = sprintf(
                'Node ID cannot be blank. Example: %s %s [NODE_ID:mandatory]',
                PHP_EOL,
                $this->command
            );
            $this->addError($error);

            return false;
        } elseif (!is_numeric($id) && !is_integer($id)) {
            $this->addError('Node ID must be integer');
            return false;
        }

        return $id;
    }
}
