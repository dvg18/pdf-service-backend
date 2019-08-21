<?php

namespace DB\Params;

/**
 * DSN parameter
 * @package DB\Params
 */
class DSN
{

    /**
     * Get MySQL DSN string
     *
     * @param string $host
     * @param string $db
     * @return string
     */
    public static function getMySqlDSN($host, $db)
    {
        return "mysql:host=$host;dbname=$db";
    }

}
