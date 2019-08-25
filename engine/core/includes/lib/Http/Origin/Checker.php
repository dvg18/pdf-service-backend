<?php

namespace Http\Origin;

/**
 * Header checker
 * @package Http\Origin
 */
class Checker
{

    /**
     * Check that http header Origin is valid (or Referrer if Origin is not defined)
     * @return boolean Returns TRUE if everything is good and FALSE otherwise
     */
    public static function exec()
    {
        if (empty($_SERVER['HTTP_ORIGIN'])) {
            $_SERVER['HTTP_ORIGIN'] = $_SERVER['HTTP_HOST'];//preg_replace('{^(http://[^/]+)/.*$}', '\1',
            // $_SERVER['HTTP_REFERER']);
        }

        return in_array(
            str_replace(array('http://', 'https://'), '', $_SERVER['HTTP_ORIGIN']),
            array(
                'www.ucni.backend',
                'www.cfs.backend',
                'www.dia.backend',
                'www.jookit.backend',
                'www.dealerconnect.backend',
                'www.usedcarsni.com',
                'www.carfinderscotland.co.uk',
                'www.driveitaway.co.uk',
                'www.jookit.com',
                'www.dealerconnect.co.uk',
                'www.pdf.backend',
                'pdf.backend',
                'pdf-service-backend'
            )
        );
    }

    public static function originWithCastedProtocol()
    {
        $headers = getallheaders();
        if (empty($headers['protocol'])) {
            return $_SERVER['HTTP_ORIGIN'];
        }
        $protocol = $headers['protocol'] . '://';
        preg_match('/^https?:\/\//', $_SERVER['HTTP_ORIGIN'], $originProtocol);
        return $protocol == $originProtocol[0]
            ? $_SERVER['HTTP_ORIGIN']
            : str_replace($originProtocol, $protocol, $_SERVER['HTTP_ORIGIN']);
    }
}
