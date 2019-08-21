<?php

namespace Pdf\String;

use Pdf\String\Formatter\Options;

class Formatter
{

    const TEXT_TYPE_REGULAR = 0;
    const TEXT_TYPE_BOLD = 1;

    protected $_entities;
    protected $_callbackUC;
    protected $_callbackUE;

    /**
     *
     * @param array $entities mapping of HTML entities to chars
     * @param callback $callbackUC callback for undefined char event
     * @param callback $callbackUE callback for undefined entity event
     */
    function __construct($entities, $callbackUC = FALSE, $callbackUE = FALSE)
    {
        $this->_entities = $entities;
        $this->setCallbackOnUndefinedChar($callbackUC);
        $this->setCallbackOnUndefinedEntity($callbackUE);
    }

    function setCallbackOnUndefinedChar($callback)
    {
        $this->_callbackUC = $callback;
    }

    function setCallbackOnUndefinedEntity($callback)
    {
        $this->_callbackUE = $callback;
    }

    /**
     * Return calculated length for string base on options
     *
     * @param string $string
     * @param \Pdf\String\Formatter\Options $options
     * @return integer calculated length of inputed string
     */
    function getStringWidth($string, Options $options)
    {
        list($string, $codes) = $this->_prepareString($string);

        $width = 0;
        $currTextType = self::TEXT_TYPE_REGULAR;
        $qty = strlen($string);
        $reg = $options->getRegChars();
        $bold = $options->getBoldChars();
        for ($i = 0; $i < $qty; $i++) {
            if ($currTextType <> $codes[$i]) {
                // if textType was changed
                $currTextType = $codes[$i];
            }

            $symbol = $string[$i];
            $ordSym = ord($symbol);
            if (empty($reg[$ordSym])) {
                $this->_callUndefinedCharCallback($symbol, $string);
                continue;
            }

            $width += ($currTextType == self::TEXT_TYPE_BOLD
                    ? $bold[$ordSym]
                    : $reg[$ordSym]);
        }
        return $width;
    }

    /**
     *
     * @param string $string
     * @param \Pdf\String\Formatter\Options $options
     * @return array
     */
    function processString($string, Options $options)
    {
        list($string, $codes) = $this->_prepareString($string);
        $cnt = strlen($string);
        $result = array();
        $currLine = '';
        $currWord = '';
        $currTextType = self::TEXT_TYPE_REGULAR;

        $lineLen = 0;
        $wordLen = 0;
        $rowCount = 0;
        $reg = $options->getRegChars();
        $bold = $options->getBoldChars();

        for ($i = 0; $i < $cnt; $i++) {
            if ($currTextType <> $codes[$i]) {
                // if textType was changed
                $currWord .= $codes[$i] == self::TEXT_TYPE_BOLD
                    ? '<b>' // from regular to bold
                    : '</b>'; // from bold to regular
                $currTextType = $codes[$i];
            }
            $symbol = $string[$i];
            $ordSym = ord($symbol);

            if (empty($reg[$ordSym])) {
                $this->_callUndefinedCharCallback($symbol, $string);
                continue;
            }

            $symbLen = ($currTextType == self::TEXT_TYPE_BOLD
                    ? $bold[$ordSym]
                    : $reg[$ordSym]);
            $lineLen += $symbLen;
            $wordLen += $symbLen;
            $currWord .= $symbol;

            // concatenate words in one line
            if ($symbol == ' ') {
                $currLine .= $currWord;
                $currWord = '';
                $wordLen = 0;
            }

            // concatenate lines in one block
            if ($lineLen > $options->getLineWidth($rowCount) && $lineLen > $wordLen) {
                if ($options->isLastLine($rowCount) && $rowCount > 0) {
                    $currWord = '';
                    break;
                }
                $result[$rowCount] = trim($currLine);
                $currLine = '';
                $lineLen = $wordLen;
                $rowCount++;
            }
        }

        $result[$rowCount] = trim($currLine . $currWord);

        if ($currTextType == self::TEXT_TYPE_BOLD) {
            $result[$rowCount] .= '</b>';
        }
        return $result;
    }

    private function _callUndefinedCharCallback($symbol, $string)
    {
        if (empty($this->_callbackUC)) {
            return FALSE;
        }
        call_user_func_array($this->_callbackUC, array($symbol, $string));
    }

    private function _callUndefinedEntityCallback($symbol, $string)
    {
        if (empty($this->_callbackUE)) {
            return FALSE;
        }
        call_user_func_array($this->_callbackUE, array($symbol, $string));
    }

    /**
     * Calculate type for each char of string (Regular/Bold)
     *
     * @param string $string
     * @return array array(string1, string of same lenght as string1 but each char determinates Regular or Bold char in smae position of stirng 1 is)
     */
    private function _prepareString($string)
    {
        $string = $this->_replaceEntities($string);
        if (preg_match_all('/&\S+;/U', $string, $matches)) {
            $cnt = count($matches[0]);
            for ($i = 0; $i < $cnt; $i++) {
                $this->_callUndefinedEntityCallback($matches[0][$i], $string);
                $string = str_ireplace($matches[0][$i], ' ', $string);
            }
        }

        $codeString = '';
        $string = str_replace('</b>', '<b>', $string);
        $strParts = explode('<b>', $string);
        $cnt = count($strParts);
        for ($i = 0; $i < $cnt; $i++) {
            $codeString .= str_repeat(
                $i % 2 == self::TEXT_TYPE_BOLD
                    ? self::TEXT_TYPE_BOLD
                    : self::TEXT_TYPE_REGULAR, strlen($strParts[$i])
            );
        }
        $string = strip_tags($string);
        return array($string, $codeString);
    }

    private function _replaceEntities($string)
    {
        foreach ($this->_entities as $entity => $code) {
            $string = str_ireplace($entity, chr($code), $string);
        }
        return $string;
    }

}
