<?php

namespace Test\wkhtmltopdf;

$_SERVER['HTTP_HOST'] = $_SERVER['HTTP_REQUESTED_ENGINE'] = 'ucni.backend';
require_once dirname(dirname(dirname(dirname(dirname(dirname(dirname(__FILE__))))))) . '/config.inc.php';
require_once CFG_PATH_COMMON_ENGINE . CFG_PATH_LIB_WKHTMLTOPDF . 'wkhtmltopdf.class.php';

class wkhtmltopdfTest extends \PHPUnit_Framework_TestCase
{

    private static $html;
    private static $outputFile;
    private static $testPdfFile;

    public static function setUpBeforeClass()
    {
        self::$html = file_get_contents(CFG_PATH_COMMON_ENGINE . CFG_PATH_LIB_WKHTMLTOPDF . '_Test/test_content.html');
        self::$testPdfFile = CFG_PATH_COMMON_ENGINE . CFG_PATH_LIB_WKHTMLTOPDF . '_Test/test_wkhtmltopdf.pdf';
        self::$outputFile = CFG_PATH_TMP . 'test_wkhtmltopdf.pdf';
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

    public static function tearDownAfterClass()
    {
        unlink(self::$outputFile);
    }

}
