<?php

class BaseClass
{
    /**
     * @return string
     */
    function __toString()
    {
        return $this->__toStringWithPad(0);
    }

    /**
     * @param int $level
     * @return string
     */
    private function __toStringWithPad($level = 0)
    {
        $out = '';
        $outObj = $this->_getPreparedObject($level);
        $str = get_class($this) . substr(print_r($outObj, TRUE), 9);
        $padString = str_repeat(' ', $level * 4);
        $lines = explode("\n", $str);
        foreach ($lines as $idx => $line) {
            $out .= ($idx == 0
                    ? ''
                    : $padString) . $line . "\n";
        }
        return $out;
    }

    /**
     * @param $level
     * @return BaseClass
     */
    private function _getPreparedObject($level)
    {
        $_this = new \BaseClass();
        $props = $this->_getProperties4Print();
        foreach ($props as $pName => $pVal) {
            try {
                $_this->$pName = $this->_toStringProcessValue($pName, $pVal, $level);
            } catch (Exception $e) {

            }
        }
        return $_this;
    }

    /**
     * @return array
     */
    protected function _getProperties4Print()
    {
        return get_object_vars($this);
    }

    /**
     * @param $pName
     * @param $pVal
     * @param $level
     * @return mixed
     * @throws Exception
     */
    private function _toStringProcessValue($pName, $pVal, $level)
    {
        if (is_object($pVal)) {
            if ($this->_noNeedPrintProp($pName)) {
                throw new Exception('No need print: ' . $pName);
            } else {
                if (method_exists($pVal, '__toStringWithPad')) {
                    return $pVal->__toStringWithPad($level + 1);
                } else {
                    return print_r($pVal, TRUE);
                }
            }
        } elseif (is_array($pVal)) {
            $_tmpArr = array();
            foreach ($pVal as $key => $val) {
                try {
                    $_tmpArr[$key] = $this->_toStringProcessValue($pName, $val, $level);
                } catch (Exception $e) {

                }
            }
            return print_r($_tmpArr, TRUE);
        }
        return $pVal;
    }

    /**
     * @param $pName
     * @return bool
     */
    protected function _noNeedPrintProp($pName)
    {
        return strpos($pName, '_') === 0 || $pName == 'User'; // TODO User->User
    }

}
