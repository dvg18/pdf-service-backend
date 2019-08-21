<?php

namespace DB;

class _FileItem
{
    public static $tableName = 'file_item';
    public static $ptfMapping = array(
        'Id' => 'id',
        'Path' => 'path',
        'Name' => 'name',
        'Type' => 'type',
        'State' => 'state',
    );
    
    public static function getTableName()
    {
        return CFG_MYSQL_COMMON_DATABASE . '.' . self::$tableName;
    }

    public static function getPropertyToFieldMapping()
    {
        return self::$ptfMapping;
    }

}
