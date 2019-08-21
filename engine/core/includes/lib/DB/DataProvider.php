<?php

use DB\API;
use Storage\CommonInterface;

class FileItem extends BaseClass //implements Item
{
    /** @var int */
    const FILE_TYPE_UNKNOWN = 0;
    /** @var int */
    const FILE_TYPE_HTML = 1;
    /** @var int */
    const FILE_TYPE_PDF = 2;

    /** @var int Unique ID */
    public $Id;

    /** @var string Path to file */
    public $Path;

    /** @var string Name of file */
    public $Name;

    /** @var string Type of file (PDF, HTML and so on) */
    public $Type;

    /** @var */
    public $State;

}

/**
 * Class for working with MySql storage
 * @package Queue
 */
class DataProvider implements CommonInterface
{

    /**
     * @inheritdoc
     */
    public function getById($id)
    {
        $item = API::selectOne('SELECT * FROM `file_item` WHERE id = :id', array(':id' => $id));
        return empty($item)
            ? FALSE
            : $this->_extractItemFromDB($item);
    }

    /**
     * @inheritdoc
     */
    public function getAll($condition = '', $begin = 0, $count = 0)
    {
        $itemsIds = API::selectAll(
            'SELECT * FROM `file_item`'
            . (empty($condition)
                ? ''
                : "WHERE $condition")
            . (empty($count)
                ? ''
                : "LIMIT $begin, $count")
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
     * @param \BaseClass $item
     * @return FileItem
     */
    private function _extractItemFromDB($item)
    {
        $fileItem = new FileItem();
        $fileItem->Id = $item->id;
        $fileItem->Path = $item->path;
        $fileItem->Name = $item->name;
        $fileItem->Type = $item->type;
        $fileItem->State = $item->state;
        return $fileItem;
    }

    /**
     * @inheritdoc
     */
    public function getCounts($condition = '')
    {
        $count = API::selectOne(
            'SELECT count(id) AS count FROM `file_item` '
            . (empty($condition)
                ? ''
                : "WHERE $condition")
        );
        return $count->count;
    }

    /**
     * @inheritdoc
     * @return BaseClass|FileItem
     */
    public function create(BaseClass $entity)
    {
        $entity->Id = API::insert(
            'INSERT INTO `file_item`
                (`path`,`name`, `state`, `type`, `inserted_date`)
                VALUES (:path, :name, :state, :type, :inserted_date)',
            array(
                ':path' => $entity->Path,
                ':name' => $entity->Name,
                ':state' => $entity->State,
                ':type' => $entity->Type,
                ':inserted_date' => date('Y-m-d H:i:s')
            )
        );
        return $entity;
    }

    /**
     * @inheritdoc
     */
    public function update(BaseClass $entity)
    {
        return API::execute(
            'UPDATE `file_item`
                SET
                  `path`=:path,
                  `name` =:name,
                  `state`=:state,
                  `type`=:type
                WHERE id=:id',
            array(
                ':path' => $entity->Path,
                ':name' => $entity->Name,
                ':state' => $entity->State,
                ':type' => $entity->Type,
                ':id' => $entity->Id
            )
        );
    }

    /**
     * @inheritdoc
     */
    public function remove($entityId)
    {
        return API::execute('DELETE FROM `file_item` WHERE id = :id', array(':id' => $entityId));
    }

}
