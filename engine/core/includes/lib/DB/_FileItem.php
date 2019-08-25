<?php

namespace DB;

class _FileItem
{
    /**
     * @var string
     */
    public static $tableName = 'file_item';

    /**
     * @var array
     */
    public static $ptfMapping = array(
        'Id' => 'id',
        'Path' => 'path',
        'Name' => 'name',
        'Type' => 'type',
        'State' => 'state',
    );

    /**
     * @return string
     */
    public static function getTableName()
    {
        return CFG_MYSQL_COMMON_DATABASE . '.' . self::$tableName;
    }

    /**
     * @return array
     */
    public static function getPropertyToFieldMapping()
    {
        return self::$ptfMapping;
    }

}
