<?php

namespace DB;

use BaseClass;
use Exception;
use Storage\CommonInterface;

/**
 * Class for working with MySql storage
 * @package DB
 */
class AbstractManager implements CommonInterface
{
    /** @var string */
    private $tableName;

    /** @var array */
    private $ptfMapping;

    /** @var string */
    private $entityName;

    /**
     * AbstractManager constructor.
     * @param $entityName
     */
    public function __construct($entityName)
    {
        $factoryClass = 'DB\_' . $entityName;
        $this->tableName = $factoryClass::getTableName();
        $this->ptfMapping = $factoryClass::getPropertyToFieldMapping();
        $this->entityName = $entityName;
    }

    /**
     * @inheritdoc
     * @throws Exception
     */
    public function getById($id)
    {
        $item = API::selectOne('SELECT * FROM ' . $this->tableName . ' WHERE id = :id', array(':id' => $id));
        return empty($item)
            ? FALSE
            : $this->_extractItemFromDB($item);
    }

    /**
     * @inheritdoc
     * @throws Exception
     */
    public function getAll($condition = '', $begin = 0, $count = 0)
    {
        $itemsIds = API::selectAll(
            'SELECT * FROM ' . $this->tableName . ' '
            . (empty($condition)
                ? ''
                : 'WHERE ' . $condition)
            . (empty($count)
                ? ''
                : 'LIMIT ' . $begin . $count)
        );
        $items = array();
        foreach ($itemsIds AS $item) {
            $items[] = $this->_extractItemFromDB($item);
        }
        return $items;
    }

    /**
     * Extract entity from DB
     *
     * @param BaseClass $item
     * @return FileItem
     */
    private function _extractItemFromDB($item)
    {
        $className = 'DB\\' . $this->entityName;
        $entityItem = new $className;
        foreach ($this->ptfMapping as $propertyName => $propertyValue) {
            $entityItem->$propertyName = $item->$propertyValue;
        }
        return $entityItem;
    }

    /**
     * @inheritdoc
     */
    public function getCounts($condition = '')
    {
        $count = API::selectOne(
            'SELECT count(id) AS count FROM ' . $this->tableName . ' '
            . (empty($condition)
                ? ''
                : 'WHERE ' . $condition)
        );
        return $count->count;
    }

    /**
     * @inheritdoc
     * @return BaseClass|FileItem
     * @throws Exception
     */
    public function create(BaseClass $entity)
    {
        $values = '';
        foreach (array_shift($this->ptfMapping) as $key => $value) {
            $values .= $entity->$key . ',';
        }
        $entity->Id = API::insert(
            'INSERT INTO ' . $this->tableName . ' '
            . '(' . implode(',', array_shift($this->ptfMapping)) . ', `inserted_date`)
                VALUES (' . $values . ',' . date('Y-m-d H:i:s') . ')'
        );
        return $entity;
    }

    /**
     * @inheritdoc
     */
    public function update(BaseClass $entity)
    {
        $values = '';
        foreach (array_shift($this->ptfMapping) as $key => $value) {
            $values .= $entity->$key . '=' . $entity->$value . ' ,';
        }
        return API::execute(
            'UPDATE ' . $this->tableName
            . 'SET ' . $values .
            ' WHERE id=' . $entity->Id
        );
    }

    /**
     * @inheritdoc
     */
    public function remove($entityId)
    {
        return API::execute('DELETE FROM ' . $this->tableName . ' WHERE id = :id', array(':id' => $entityId));
    }
}