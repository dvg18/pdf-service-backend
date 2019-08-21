<?php

namespace Converter;

use wkhtmltopdf;

class Pdf
{

    /**
     *
     * @param string $source
     * @return string
     */
    public static function convert($source)
    {
        //include_once CFG_PATH_COMMON_ENGINE . CFG_PATH_LIB . 'wkhtmltopdf/' . 'wkhtmltopdf.class.php';
        $wk = new wkhtmltopdf();
        $wk->setHtmlContent($source);
        $tmpFileName = tempnam(CFG_FOLDER_UPLOADED_TMP, 'pdf');
        $wk->setOutputFile($tmpFileName);
        $wk->exec();

        $pdf = file_get_contents($tmpFileName);

        if (file_exists($tmpFileName)) {
            unlink($tmpFileName);
        }

        return $pdf;
    }

}
