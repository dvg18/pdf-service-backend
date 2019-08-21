<?php

class ActionHtml extends AbstractFileAction implements Actionable
{
    /**
     * @return bool
     */
    public static function get()
    {
        if (empty($file = parent::getFileWithCheck($_GET['id']))) {
            return TRUE;
        }
        if ($file->Type != FileItem::FILE_TYPE_HTML) {
            echo json_encode(array('error' => self::FILE_IS_NOT_HTML));
            return TRUE;
        }
        echo File::getFileContent($file);
        return TRUE;
    }

    /**
     * @return bool
     */
    public static function post()
    {
        if (empty($_REQUEST['html'])) {
            echo json_encode(array('error' => self::EMPTY_HTML));
            return TRUE;
        }
        $file = new File('tmp');
        $file->setContent($_REQUEST['html']);
        echo $file->save(CFG_FOLDER_UPLOADED_TMP, FileItem::FILE_TYPE_HTML)->Id;
        return TRUE;
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