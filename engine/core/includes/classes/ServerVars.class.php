<?php

class ServerVars
{

    protected static function getHeader($header, $defaultValue = '')
    {
        return $GLOBALS['Request']->issetParam($header)
            ? $GLOBALS['Request']->get($header)
            : $defaultValue;
    }

    public static function getIp()
    {
        $tmpIp = self::getHeader('HTTP_X_REAL_IP',
                self::getHeader('REMOTE_ADDR'));
        $ip = self::getHeader('HTTP_X_FORWARDED_FOR', $tmpIp);
        return $ip;
    }

    public static function getProxy()
    {
        $tmpIp = self::getHeader('HTTP_X_REAL_IP',
                self::getHeader('REMOTE_ADDR'));
        $proxy = self::getHeader('HTTP_X_FORWARDED_FOR') != false
            ? $tmpIp
            : '';
        return $proxy;
    }

    public static function getHttpHost()
    {
        return self::getHeader('HTTP_X_REAL_HOST', self::getHeader('HTTP_HOST'));
    }

    public static function getHTTPReferer()
    {
        return self::getHeader('HTTP_REFERER');
    }

    public static function getUserAgent()
    {
        return self::getHeader('HTTP_USER_AGENT');
    }

    public static function getRequestURI()
    {
        return self::getHeader('REQUEST_URI');
    }

    public static function getPhpSelf()
    {
        return self::getHeader('PHP_SELF');
    }

    public static function setDefaultRequestPage()
    {
        $uri = self::getRequestURI();
        if (!empty($uri)) {
            $tmp = explode('?', $uri);
            $page = $tmp[0];
        } else {
            $page = self::getPhpSelf();
        }
        $GLOBALS['Request']->set('REQUEST_PAGE', $page);
    }

}
