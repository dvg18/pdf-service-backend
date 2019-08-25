<?php

namespace Pdf;

use Converter\Pdf;
use File;
use DB\FileItem;

/**
 * Class that makes PDF files and //can send headers to download them
 */
class API
{
    /**
     * Make PDF file and send headers to download it
     *
     * @param string $html
     * @param string $filename
     * @return bool|false|string
     */
    public static function getPDFFromHTML($html, $filename = '')
    {
        if (empty($html)) {
            return FALSE;
        }
        return File::getFileForDownload(self::getPDFFile($html));
    }

    /**
     * Make PDF file and get path to it
     *
     * @param string $html
     * @return FileItem
     */
    public static function getPDFFile($html)
    {
        return self::_saveContentToTmpFile($html, self::_makeTempFile());
    }

    /**
     * Make temp PDF file
     * @return string
     */
    private static function _makeTempFile()
    {
        return tempnam(CFG_FOLDER_UPLOADED_TMP, 'temp_pdf_');
    }

    /**
     * Save HTML to PDF file
     *
     * @param string $html
     * @param string $tmpFile
     * @return FileItem
     */
    private static function _saveContentToTmpFile($html, $tmpFile)
    {
        $file = new File(basename($tmpFile));
        $file->setContent(self::fillContentFromHtml($html));
        return $file->save(CFG_FOLDER_UPLOADED_TMP, FileItem::FILE_TYPE_PDF);
    }

    /**
     *
     * @param string $html
     * @return string
     */
    private static function fillContentFromHtml($html)
    {
        return Pdf::convert($html);
    }

}
