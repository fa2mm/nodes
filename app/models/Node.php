<?php

namespace app\models;

use \app\core\base\Base;

/**
 * Class Node
 * @package app\models
 * @author Olexander Mokhonko <2day@ua.fm>
 * Date: 2018-07-12
 */
class Node extends Base
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
     * @return string
     */
    public static function tableName()
    {
        return 'category';
    }

    /**
     * @param string $title
     * @param integer $parentId
     * @return bool
     * @throws \Exception
     */
    public function add($title, $parentId)
    {
        if (empty($parentId)) {
            $parentId = self::query()->findMainNode();
            if (false === $parentId) {
                // Table is empty. Let save first node.
                $id = self::query()->save($title, 1, 2, 1);
                $this->addResultMessage('Node "' . $title . '" has been added with id #' . $id);
                return true;
            }
        }

        $parent = self::query()->findByPk($parentId);

        if (false === $parent) {
            $this->addError('Error, node with ID #' . $parentId . ' not found');
            return false;
        }

        $id = self::query()->addNewNode($title, $parent);

        if (false === $id) {
            return false;
        } else {
            $this->addResultMessage('Node "' . $title . '" has been added with id #' . $id);
            return true;
        }
    }

    /**
     * @param integer $id
     * @return bool
     * @throws \Exception
     */
    public function remove($id)
    {
        $node = self::query()->findByPk($id);

        if (false === $node) {
            $this->addError('Error, node with ID #' . $id . ' not found');
            return false;
        }

        self::query()->removeAll($node);

        $this->addResultMessage('Node ID #' . $id . ' has been deleted');
        return true;
    }

    /**
     * @param integer   $id
     * @param string    $newTitle
     * @return bool
     * @throws \Exception
     */
    public function rename($id, $newTitle)
    {
        $node = self::query()->findByPk($id);

        if (false === $node) {
            $this->addError('Error, node with ID #' . $id . ' not found');
            return false;
        }

        self::query()->updateTitle($id, $newTitle);

        $this->addResultMessage('Node ID #' . $id . ' has been renamed');
        return true;
    }

    /**
     * @param integer $id
     * @return bool
     * @throws \Exception
     */
    public function up($id)
    {
        $child = self::query()->findByPk($id);

        if (false === $child) {
            $this->addError('Error, node with ID #' . $id . ' not found');
            return false;
        }

        $parent = self::query()->findParent($child);

        if (false === $parent) {
            $this->addError('Error, node with ID #' . $id . ' already at the top (does not have parent)');
            return false;
        }

        if (self::query()->change($parent, $child)) {
            $this->addResultMessage('Node ID #' . $id . ' has been moved at the top');
            return true;
        }
    }

    /**
     * @param integer $id
     * @return bool
     * @throws \Exception
     */
    public function down($id)
    {
        $node = self::query()->findByPk($id);

        if (false === $node) {
            $this->addError('Error, node with ID #' . $id . ' not found');
            return false;
        }

        $firstChild = self::query()->findFirstChild($node);

        if (false === $firstChild) {
            $this->addError('Error, node with ID #' . $id . ' already at the bottom (does not have children)');
            return false;
        }

        if (self::query()->change($node, $firstChild)) {
            $this->addResultMessage('Node ID #' . $id . ' has been moved at the bottom');
            return true;
        }
    }

    /**
     * @return NodeQuery
     */
    public static function query()
    {
        return new NodeQuery();
    }
}
