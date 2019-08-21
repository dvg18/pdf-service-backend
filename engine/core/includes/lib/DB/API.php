<?php

namespace DB;

use PDO;

/**
 * Data Base API
 * @package DB
 */
class API
{

    /**
     * Server mode constants
     */
    const USE_SINGLE_SRV = 0;

    /**
     * Server mode constants
     */
    const USE_FULL_FUNC = 1;

    /**
     * Server mode constants
     */
    const USE_READ_ONLY = 2;

    /**
     * Database Handles
     *
     * @var PDO
     */
    private static $_dbhs = array();

    /**
     * Last insert Id
     *
     * @var int
     */
    private static $_lastInsertId;

    /**
     * Bonds between connection params and roles
     *
     * @var string
     */
    private static $_connectionParams = array();

    /**
     * Array of prepared statements
     *
     * @var array
     */
    private static $_preparedStatements = array();

    /**
     * Bind connection params to role
     *
     * @param \DB\Params\Connection $params
     * @param int $role Role
     * @return void
     * @throws \Exception
     */
    public static function bind(Params\Connection $params, $role)
    {
        if (isset(self::$_connectionParams[$role])) {
            throw new \Exception(
                'duplicate role to connection params relation (file: ' . __FILE__ . ', line: ' . __LINE__ . ')'
            );
        }
        self::$_connectionParams[$role] = $params;
    }

    /**
     * Reconnect to DB using role
     *
     * @param bool|int $role
     */
    public static function forceReconnect($role = FALSE)
    {
        self::$_preparedStatements = array();
        if ($role === FALSE) {
            $reconnectTo = self::$_connectionParams;
        } elseif (isset(self::$_connectionParams[$role])) {
            $reconnectTo = array($role => self::$_connectionParams[$role]);
        } else {
            $reconnectTo = array();
        }

        foreach ($reconnectTo as $role => $params) {
            self::_connect($params, $role);
        }
    }

    /**
     * Connect using connection params and role
     *
     * @param \DB\Params\Connection $params
     * @param int $role Role
     * @return PDO
     */
    private static function _connect(Params\Connection $params, $role)
    {
        $dbh = new PDO($params->dsn, $params->username, $params->password);
        if (!empty($params->encoding)) {
            $dbh->exec('SET NAMES \'' . $params->encoding . '\'');
        }
        $dbh->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

        if ($role == self::USE_SINGLE_SRV) {
            self::_setDbh(self::USE_FULL_FUNC, $dbh);
            self::_setDbh(self::USE_READ_ONLY, $dbh);
        }
        self::_setDbh($role, $dbh);

        if ($dbh == FALSE && $role == self::USE_READ_ONLY) {
            self::_setDbh(self::USE_FULL_FUNC, self::_getDbh(self::USE_FULL_FUNC));
        }

        return $dbh;
    }

    /**
     * Set DBH for role
     *
     * @param int $role Role
     * @param PDO $dbh PDO object
     * @return void
     */
    private static function _setDbh($role, $dbh)
    {
        self::$_dbhs[$role] = $dbh;
    }

    /**
     * Get DBH
     *
     * @staticvar array $prevCall Role previous call
     * @param int $role Role
     * @return PDO|boolean
     */
    private static function _getDbh($role)
    {
        static $prevCall = array();
        $needReconnect = empty($prevCall[$role])
            ? TRUE
            : (time() - $prevCall[$role] > 28800);
        $prevCall[$role] = time();
        if ($needReconnect) {
            return FALSE;
        }
        return isset(self::$_dbhs[$role])
            ? self::$_dbhs[$role]
            : FALSE;
    }

    /**
     * Execute SQL
     *
     * @param string $sql SQL query
     * @param array $vars Array of params to bind. Example: array(":id" => 2)
     * @return integer
     * @throws \Exception
     */
    public static function execute($sql, $vars = array())
    {
        $statement = self::_tryGetPreparedStatement($sql);
        $statement->execute($vars);
        return $statement->rowCount();
    }

    /**
     * Select multiple rows from DB
     *
     * @param string $sql SQL query
     * @param array $vars Array of params to bind. Example: array(":id" => 2)
     * @param int $fetchType Use \PDO::FETCH_* constants
     * @return \BaseClass[]
     * @throws \Exception
     */
    public static function selectAll($sql, $vars = array(), $fetchType = PDO::FETCH_OBJ)
    {
        $statement = self::_tryGetPreparedStatement($sql);
        return $statement->execute($vars)
            ? $statement->fetchAll($fetchType)
            : array();
    }

    /**
     * Try to get prepared statement
     *
     * @param string $sql
     * @return \PDOStatement
     * @throws \Exception
     */
    private static function _tryGetPreparedStatement($sql)
    {
        $sqlChecksum = crc32($sql);
        if (key_exists($sqlChecksum, self::$_preparedStatements)) {
            $statement = self::$_preparedStatements[$sqlChecksum];
        } else {
            $statement = self::_getPreparedStatement($sql);
            self::$_preparedStatements[$sqlChecksum] = $statement;
        }

        return $statement;
    }

    /**
     * Select one row from DB
     *
     * @param string $sql SQL query
     * @param array $vars Array of params to bind. Example: array(":id" => 2)
     * @return array
     * @throws \Exception
     */
    public static function selectOne($sql, $vars = array())
    {
        $statement = self::_tryGetPreparedStatement($sql);
        return $statement->execute($vars)
            ? $statement->fetch(PDO::FETCH_OBJ)
            : array();
    }

    /**
     * Insert into DB
     *
     * @param string $sql SQL query
     * @param array $vars Array of params to bind. Example: array(":id" => 2)
     * @return int Last insert ID or <b>0</b> on error.
     * @throws \Exception
     */
    public static function insert($sql, $vars = array())
    {
        $statement = self::_tryGetPreparedStatement($sql);
        $dbh = self::_getDbhForQuery(FALSE);
        $result = $statement->execute($vars)
            ? $dbh->lastInsertId()
            : 0;
        if ($result) {
            self::$_lastInsertId = $result;
        }
        return $result;
    }

    /**
     * Get prepared statement
     * ToDo: need to hide it BUT use!!!! Need to save prepared statements somehow!!!!
     *
     * @param string $sql SQL string for preparing. There can be used binding like "id = :id AND name = :name" etc.
     * @param boolean $role Role
     * @return \PDOStatement
     * @throws \Exception
     */
    public static function _getPreparedStatement($sql, $role = FALSE)
    {
        $dbh = self::_getDbhForQuery($role);
        return $dbh->prepare($sql);
    }

    /**
     * Get DBH for query
     *
     * @param int $role Role
     * @return PDO
     * @throws \Exception
     */
    private static function _getDbhForQuery($role)
    {
        return empty($role) || empty(self::$_connectionParams[$role])
            ? self::_getConnection(self::USE_FULL_FUNC)
            : self::_getConnection($role);
    }

    /**
     * Get connection
     *
     * @param int $role Role
     * @return PDO
     * @throws \Exception
     */
    private static function _getConnection($role)
    {
        if (empty(self::$_connectionParams[$role])) {
            if (empty(self::$_connectionParams[self::USE_SINGLE_SRV])) {
                throw new \Exception(
                    'nonexistent dsn-role relation (role=' . $role . ') (file:' . __FILE__ . ', line:' . __LINE__ . ')'
                );
            } else {
                $role = self::USE_SINGLE_SRV;
            }
        }

        $dbh = self::_getDbh($role);
        return $dbh == FALSE
            ? self::_connect(self::$_connectionParams[$role], $role)
            : $dbh;
    }

    /**
     * Get last insert id
     *
     * @return int Last insert id
     */
    public static function getLastInsertId()
    {
        return self::$_lastInsertId;
    }

    /**
     * Close all connections
     *
     * @return void
     */
    public static function closeConnections()
    {
        foreach (self::$_dbhs as &$dbh) {
            $dbh = NULL;
        }
        self::$_dbhs = array();
        self::$_preparedStatements = array();
    }

}

require_once 'DB/Params/DSN.php';
require_once 'DB/Params/Connection.php';

$connectionFull = new Params\Connection(
    Params\DSN::getMySqlDSN(CFG_MYSQL_HOST, CFG_MYSQL_DATABASE),
    CFG_MYSQL_USER,
    CFG_MYSQL_PASS
);

API::bind($connectionFull, API::USE_SINGLE_SRV);
