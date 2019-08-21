<?php

class ActionFile extends AbstractFileAction implements Actionable
{
    /**
     * @return bool
     */
    public static function get()
    {
        //Todo check for empty id
        if (empty($file = parent::getFileWithCheck($_GET['id']))) {
            return TRUE;
        }
        if (empty($result = File::getFileForDownload($file))) {
            return FALSE;
        }
        echo $result;
        return TRUE;
    }

    /**
     * @return bool
     */
    public static function delete()
    {
        if (empty($file = parent::getFileWithCheck($_GET['id']))) {
            return TRUE;
        }
        if (empty($result = File::removeFile($file))) {
            return FALSE;
        }
        echo $result;
        return TRUE;

    }

    /**
     * @return bool
     */
    public static function post()
    {
        return FALSE;
    }

    /**
     * @return bool
     */
    public static function put()
    {
        return FALSE;
    }
}