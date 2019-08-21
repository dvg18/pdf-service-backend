<?php

abstract class AbstractFileAction
{
    /** @var string */
    const FILE_DOES_NOT_EXIST = 'File doesn\'t exist';

    /** @var string */
    const EMPTY_ID = 'id can\'t be empty';

    /** @var string */
    const FILE_IS_NOT_HTML = 'This file isn\'t html-file';

    /** @var string */
    const EMPTY_HTML = 'HTML body is empty';

    /**
     * @param $id
     * @return bool|FileItem
     */
    protected static function getFileWithCheck($id)
    {
        if (!self::check4EmptyId($id)) {
            return FALSE;
        }
        if (empty($file = File::getFile($id))) {
            echo json_encode(array('error' => self::FILE_DOES_NOT_EXIST));
            return FALSE;
        } else {
            return $file;
        }
    }

    /**
     * @param int $id
     * @return bool
     */
    protected static function check4EmptyId($id)
    {
        if (empty($id)) {
            echo json_encode(array('error' => self::EMPTY_ID));
            return FALSE;
        }
        return TRUE;
    }
}