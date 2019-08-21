<?php

namespace Pdf\String\Formatter;

class Options
{

    // Line Width Type
    const LWT_SINGLELINE = 0;
    const LWT_MULTILINE = 1;

    /**
     *
     * @var array $_regular mapping char code to width for regular font
     */
    private $_regular;

    /**
     *
     * @var array $_bold mapping char code to width for bold font
     */
    private $_bold;

    /**
     *
     * @var integer $_lineWidthType determinate will all line same width or each has own width set
     */
    private $_lineWidthType;

    /**
     *
     * @var mixed $_lineWidth width for each line, if integer then all line has same width, else array with width for certain line number
     */
    private $_lineWidth;

    /**
     *
     * @param array $regChars array of widths for each regular char
     * @param array $boldChars array of widths for each bold char
     * @param mixed $lineWidth if mltiline then array of widths for each line, else integer = width of line
     */
    function __construct($regChars, $boldChars, $lineWidth)
    {
        $this->_regular = $regChars;
        $this->_bold = $boldChars;
        $this->_setLineWidth($lineWidth);
    }

    /**
     * Set _lineWidth & _lineWidthType properties value
     *
     * @param mixed $lineWidth
     */
    private function _setLineWidth($lineWidth)
    {
        $this->_lineWidthType = is_array($lineWidth)
            ? self::LWT_MULTILINE
            : self::LWT_SINGLELINE;
        $this->_lineWidth = $lineWidth;
    }

    /**
     * Return width for requested line number,
     * if no info about width for requested line number then thorw Exception
     *
     * @param integer $lineNum number of line
     * @return integer width for line with number $lineNum
     */
    function getLineWidth($lineNum)
    {
        return $this->_lineWidthType == self::LWT_SINGLELINE
            ? $this->_lineWidth
            : $this->_getLineWidth4MultiLine($lineNum);
    }

    private function _getLineWidth4MultiLine($lineNum)
    {
        if (isset($this->_lineWidth[$lineNum])) {
            return $this->_lineWidth[$lineNum];
        }
        throw new \Exception('Pdf\String\Fromatter\Options::_getLineWidth4MultiLine(): '
        . 'Request width for not exists line number ' . $lineNum);
    }

    /**
     * Return TRUE if line number is last line else FALSE
     *
     * @param integer $lineNum number of checked line
     * @return boolean TRUE if line number is last line else FALSE
     */
    function isLastLine($lineNum)
    {
        return $this->_lineWidthType == self::LWT_SINGLELINE
            ? FALSE
            : ($lineNum + 1) == count($this->_lineWidth);
    }

    function getRegChars()
    {
        return $this->_regular;
    }

    function getBoldChars()
    {
        return $this->_bold;
    }

}
