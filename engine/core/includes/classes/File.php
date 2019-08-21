<?php

use DB\Wrapper;

class File
{
    /** @var string */
    private $Name = 'file';
    /** @var string */
    private $Content = '';

    /**
     *
     * @param string $name
     * @param string $content
     */
    public function __construct($name = 'file', $content = '')
    {
        $this->Name = date("dmY_His_") . $name;
        $this->Content = $content;
    }

    /**
     * @param string $Content
     */
    public function setContent($Content)
    {
        $this->Content = $Content;
    }

    /**
     * @param string $path
     * @param int $type
     * @return FileItem
     */
    public function save($path, $type = FileItem::FILE_TYPE_UNKNOWN)
    {
        file_put_contents($path . $this->Name, $this->Content);
        /** @var FileItem $fileItem */
        $fileItem = new FileItem();
        $fileItem->Name = $this->Name;
        $fileItem->Path = $path;
        $fileItem->Type = $type;
        $fileItem->State = 0;
        $DP = new Wrapper(get_class($fileItem));
        return $DP->create($fileItem);
    }

    /**
     * @param int|FileItem $file
     * @return false|string
     */
    public static function getFileContent($file)
    {
        if (empty($file = self::checkFile($file))) {
            return FALSE;
        }
        return file_get_contents($file->Path . '/' . $file->Name);
    }

    /**
     * @param int|FileItem $file
     * @return bool|FileItem
     */
    public static function checkFile($file)
    {
        if (!is_object($file)) {
            $file = self::getFile($file);
        }
        if (!self::fileExist($file)) {
            return FALSE;
        }
        return $file;
    }

    /**
     * @param $id
     * @return bool|FileItem
     */
    public static function getFile($id)
    {
        $DP = new Wrapper('FileItem');
        return $DP->getById($id);
    }

    /**
     * @param FileItem $file
     * @return bool
     */
    public static function fileExist($file)
    {
        return (empty($file) || !file_exists($file->Path . '/' . $file->Name))
            ? FALSE
            : TRUE;
    }

    /**
     * @param int|FileItem $file
     * @return bool|false|string
     */
    public static function getFileForDownload($file)
    {
        if (empty($file = self::checkFile($file))) {
            return FALSE;
        }
        switch ($file->Type) {
            case FileItem::FILE_TYPE_PDF:
                header('Content-Type: application/pdf');
                header('Content-Disposition: attachment; filename="' . $file->Name . '.pdf"');
                break;
            case FileItem::FILE_TYPE_HTML:
                header('Content-Type: text/html');
                header('Content-Disposition: attachment; filename="' . $file->Name . '.html"');
                break;
        }
        return file_get_contents($file->Path . '/' . $file->Name);//File::getFileContent($tmpFile);
    }

    /**
     * @param int|FileItem $file
     * @return bool
     */
    public static function removeFile($file)
    {
        if (!is_object($file)) {
            $file = self::getFile($file);
        }
        if (empty($file)) {
            return FALSE;
        }
        $DP = new DataProvider();
        $DP->remove($file->Id);
        if (!self::fileExist($file)) {
            return FALSE;
        }
        return unlink($file->Path . '/' . $file->Name);
    }

}
