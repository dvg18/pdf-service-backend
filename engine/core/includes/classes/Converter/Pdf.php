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
