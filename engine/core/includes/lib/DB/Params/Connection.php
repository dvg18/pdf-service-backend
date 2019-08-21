<?php

namespace DB\Params;

/**
 * Connection parameters
 * @package DB\Params
 */
class Connection
{

    /**
     *
     * @var string DSN string
     */
    public $dsn;

    /**
     *
     * @var string Username
     */
    public $username;

    /**
     *
     * @var string Password
     */
    public $password;

    /**
     *
     * @var string Database encoding
     */
    public $encoding;

    /**
     * Class constructor
     *
     * @param string $dsn DSN string
     * @param string $username Username
     * @param string $password Password
     * @param string $encoding Database encoding
     */
    public function __construct($dsn, $username, $password, $encoding = 'latin1')
    {
        $this->dsn = $dsn;
        $this->username = $username;
        $this->password = $password;
        $this->encoding = $encoding;
    }

}
