<?php

class wkhtmltopdf
{

    private $binaryPath;
    private $binaryName;
    private $params;
    private $inputFile;
    private $outputFile;
    private $HtmlContent;

    public function __construct()
    {
        $this->binaryPath = CFG_PATH_COMMON_ENGINE . CFG_PATH_LIB . 'wkhtmltopdf/';
        $this->binaryName = 'wkhtmltopdf ';
        $this->params = ' -qn --dpi 94 ';
        $this->inputFile;
        $this->outputFile;
    }

    public function setInputFile($file)
    {
        $this->inputFile = $file;
    }

    public function getInputFile()
    {
        return $this->inputFile;
    }

    public function setOutputFile($file)
    {
        $this->outputFile = $file;
    }

    public function getOutputFile()
    {
        return $this->outputFile;
    }

    public function setParams($params)
    {
        $this->params = $params;
    }

    public function setHtmlContent($html)
    {
        $this->HtmlContent = $html;
    }

    private function genInputFile($html)
    {
        // wkhtmltopdf DOES NOT WORK WITHOUT HTML file extension !!!!!
        $tmp_html_file = tempnam('tmp', time()) . '.html';
        $ret = file_put_contents($tmp_html_file, str_replace('src="https', 'src="http', $html), LOCK_EX);
        @chmod($tmp_html_file, 0666);
        return $tmp_html_file;
    }

    public function exec()
    {
        if (empty($this->inputFile) && empty($this->HtmlContent)) {
            return false;
        }

        if (empty($this->inputFile) && !empty($this->HtmlContent)) {
            $this->inputFile = $this->genInputFile($this->HtmlContent);
        }

        $cmd = $this->binaryPath . $this->binaryName . ' ' . $this->params . ' ' .
            $this->inputFile . ' ' . $this->outputFile;

        $output = exec($cmd);

        // clean input tmp files
        if (file_exists($this->inputFile)) {
            unlink($this->inputFile);
        }

        $simpleTmpFile = str_replace('.html', '', $this->inputFile);
        if (file_exists($simpleTmpFile)) {
            unlink($simpleTmpFile);
        }

        return $output;
    }

}
