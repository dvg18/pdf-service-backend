<?php

use Pdf\API;

class ActionPdf extends AbstractFileAction implements Actionable
{
    /**
     * @return bool
     */
    public static function get()
    {
        if (empty($file = parent::getFileWithCheck($_GET['id']))) {
            return TRUE;
        }
        if (empty($result = API::getPDFFromHTML(File::getFileContent($file), 'tmp'))) {
            echo json_encode(array('error' => self::EMPTY_HTML));
            return TRUE;
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

    /**
     * @return bool
     */
    public static function delete()
    {
        return FALSE;
    }
}