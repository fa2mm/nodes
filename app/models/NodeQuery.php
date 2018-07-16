<?php

namespace app\models;

use \tecsvit\ObjectHelper as OH;
use \app\core\base\Db;

/**
 * Class Node
 * @package app\models
 * @author Olexander Mokhonko <2day@ua.fm>
 * Date: 2018-07-12
 */
class NodeQuery extends Node
{
    /**
     * @param string    $title
     * @param array     $parent
     * @return bool|int
     * @throws \Exception
     */
    public function addNewNode($title, $parent)
    {
        $sql = 'UPDATE ' . self::tableName()
            . ' SET rgt = rgt + 2, lft = IF(lft > :rgt, lft + 2, lft) WHERE rgt >= :rgt';
        $query = Db::getDb()->prepare($sql);
        $query->bindParam(':rgt', $parent['rgt'], \PDO::PARAM_INT);
        $this->execute($query);

        return $this->save($title, $parent['rgt'], $parent['rgt'] + 1, $parent['lvl'] + 1);
    }

    /**
     * @return bool|mixed
     * @throws \Exception
     */
    public function findMainNode()
    {
        $sql = 'SELECT id, MAX(rgt) FROM ' . self::tableName() . ' GROUP BY id, rgt;';
        $query = Db::getDb()->prepare($sql);
        if ($this->execute($query)) {
            $data = $query->fetch(\PDO::FETCH_ASSOC);
            if (false === $data) {
                return false;
            } else {
                return OH::getAttribute($data, 'id');
            }
        } else {
            return false;
        }
    }

    /**
     * @param array $node
     * @return void
     * @throws \Exception
     */
    public function removeAll($node)
    {
        $sql = 'DELETE FROM ' . self::tableName() . ' WHERE lft >= :lft AND rgt <= :rgt';
        $query = Db::getDb()->prepare($sql);
        $query->bindParam(':lft', $node['lft'], \PDO::PARAM_INT);
        $query->bindParam(':rgt', $node['rgt'], \PDO::PARAM_INT);
        $this->execute($query);

        $this->updateAfterRemove($node);
    }

    /**
     * @param array $node
     * @return bool
     * @throws \Exception
     */
    public function updateAfterRemove($node)
    {
        $sql = 'UPDATE ' . self::tableName()
            . ' SET lft = IF(lft > :lft, lft - (:rgt - :lft + 1), lft), rgt = rgt - (:rgt - :lft + 1) WHERE rgt > :rgt';
        $query = Db::getDb()->prepare($sql);
        $query->bindParam(':rgt', $node['rgt'], \PDO::PARAM_INT);
        $query->bindParam(':lft', $node['lft'], \PDO::PARAM_INT);

        return $this->execute($query);
    }

    /**
     * @param array $parentNode
     * @param array $childNode
     * @return int
     * @throws \Exception
     */
    public function change($parentNode, $childNode)
    {
        $sql = 'UPDATE ' . self::tableName() . ' SET lft = :lft, rgt = :rgt WHERE id = :id';
        $query = Db::getDb()->prepare($sql);
        $query->bindParam(':lft', $childNode['lft'], \PDO::PARAM_INT);
        $query->bindParam(':rgt', $childNode['rgt'], \PDO::PARAM_INT);
        $query->bindParam(':id', $parentNode['id'], \PDO::PARAM_INT);
        $this->execute($query);

        $sql = 'UPDATE ' . self::tableName() . ' SET lvl = lvl - 1, lft = :lft, rgt = :rgt WHERE id = :id';
        $query = Db::getDb()->prepare($sql);
        $query->bindParam(':lft', $parentNode['lft'], \PDO::PARAM_INT);
        $query->bindParam(':rgt', $parentNode['rgt'], \PDO::PARAM_INT);
        $query->bindParam(':id', $childNode['id'], \PDO::PARAM_INT);
        $this->execute($query);

        // We have unique index so we must change level in other query
        $sql = 'UPDATE ' . self::tableName() . ' SET lvl = lvl + 1 WHERE id = :id';
        $query = Db::getDb()->prepare($sql);
        $query->bindParam(':id', $parentNode['id'], \PDO::PARAM_INT);
        $this->execute($query);

        return true;
    }

    /**
     * @param string    $title
     * @param integer   $lft
     * @param integer   $rgt
     * @param integer   $lvl
     * @return boolean|integer
     * @throws \Exception
     */
    public function save($title, $lft, $rgt, $lvl)
    {
        $sql = 'INSERT ' . self::tableName() . ' SET title = :t, lft = :lft, rgt = :rgt, lvl = :lvl';
        $query = Db::getDb()->prepare($sql);
        $query->bindParam(':t', $title, \PDO::PARAM_STR);
        $query->bindParam(':lft', $lft, \PDO::PARAM_INT);
        $query->bindParam(':rgt', $rgt, \PDO::PARAM_INT);
        $query->bindParam(':lvl', $lvl, \PDO::PARAM_INT);
        if ($this->execute($query)) {
            return Db::getDb()->lastInsertId();
        } else {
            return false;
        }
    }

    /**
     * @param integer   $id
     * @param string    $newTitle
     * @return bool
     * @throws \Exception
     */
    public function updateTitle($id, $newTitle)
    {
        $sql = 'UPDATE ' . self::tableName() . ' SET title = :title WHERE id = :id';
        $query = Db::getDb()->prepare($sql);
        $query->bindParam(':title', $newTitle, \PDO::PARAM_STR);
        $query->bindParam(':id', $id, \PDO::PARAM_INT);

        return $this->execute($query);
    }

    /**
     * @param array $node
     * @return array|boolean
     * @throws \Exception
     */
    public function findParent($node)
    {
        $sql = 'SELECT * FROM ' . self::tableName() . ' WHERE lvl = :lvl - 1 AND lft < :lft AND rgt > :rgt LIMIT 1';
        $query = Db::getDb()->prepare($sql);
        $query->bindParam(':lvl', $node['lvl'], \PDO::PARAM_INT);
        $query->bindParam(':lft', $node['lft'], \PDO::PARAM_INT);
        $query->bindParam(':rgt', $node['rgt'], \PDO::PARAM_INT);

        if ($this->execute($query)) {
            return OH::getAttribute($query->fetchAll(\PDO::FETCH_ASSOC), 0, []);
        } else {
            return false;
        }
    }

    /**
     * @param array $node
     * @return array|bool
     * @throws \Exception
     */
    public function findFirstChild($node)
    {
        $sql = 'SELECT * FROM ' . self::tableName() . ' WHERE lvl = :lvl + 1 AND lft = :lft + 1 LIMIT 1';
        $query = Db::getDb()->prepare($sql);
        $query->bindParam(':lvl', $node['lvl'], \PDO::PARAM_INT);
        $query->bindParam(':lft', $node['lft'], \PDO::PARAM_INT);

        if ($this->execute($query)) {
            return OH::getAttribute($query->fetchAll(\PDO::FETCH_ASSOC), 0, []);
        } else {
            return false;
        }
    }

    /**
     * @param integer $id
     * @return boolean|array
     * @throws \Exception
     */
    public function findByPk($id)
    {
        $sql = 'SELECT * FROM ' . self::tableName() . ' WHERE id = :id';
        $query = Db::getDb()->prepare($sql);
        $query->bindParam(':id', $id, \PDO::PARAM_INT);
        if ($this->execute($query)) {
            return $query->fetch(\PDO::FETCH_ASSOC);
        } else {
            return false;
        }
    }

    /**
     * @param \PDOStatement $query
     * @return integer
     * @throws \Exception
     */
    private function execute($query)
    {
        if ($query->execute()) {
            return $query->rowCount();
        } else {
            throw new \Exception(implode($query->errorInfo(), '. '));
        }
    }
}
