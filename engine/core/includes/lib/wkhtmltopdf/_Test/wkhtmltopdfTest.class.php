<?php

namespace Test\wkhtmltopdf;

use PHPUnit\Framework\TestCase;

$_SERVER['HTTP_HOST'] = $_SERVER['HTTP_REQUESTED_ENGINE'] = 'ucni.backend';
require_once '../../../config.inc.php';
require_once CFG_PATH_COMMON_ENGINE . CFG_PATH_LIB . 'wkhtmltopdf/' . 'wkhtmltopdf.class.php';

class wkhtmltopdfTest extends TestCase
{

    private static $html;
    private static $outputFile;
    private static $testPdfFile;

    public static function setUpBeforeClass() : void
    {
        self::$html = file_get_contents(CFG_PATH_COMMON_ENGINE . CFG_PATH_LIB. 'wkhtmltopdf/' . '_Test/test_content.html');
        self::$testPdfFile = CFG_PATH_COMMON_ENGINE . CFG_PATH_LIB . 'wkhtmltopdf/' . '_Test/test_wkhtmltopdf.pdf';
        self::$outputFile = 'tmp/test_wkhtmltopdf.pdf';
    }

    public function testExec()
    {
        $wkhtmltopdf = new \wkhtmltopdf();
        $wkhtmltopdf->setHtmlContent(self::$html);
        $wkhtmltopdf->setOutputFile(self::$outputFile);
        $wkhtmltopdf->exec();

        $this->assertFileExists(self::$outputFile);
    }

    /**
     * @depends testExec
     */
    public function testPdfSize()
    {
        $this->assertEquals(filesize(self::$outputFile),
            filesize(self::$testPdfFile));
    }

    public static function tearDownAfterClass() :void
    {
        unlink(self::$outputFile);
    }

}
