<?php

namespace Framework\Mvc\Database;

use Framework\Abstractions\Errorcodes\DatabaseErrorCodes;
use Framework\Abstractions\Exceptions\DatabaseException;
use mysqli;


/**
 *================================================================
 *framework/Database/Mysql.class.php
 *Database operation class
 *================================================================
 */
class Database
{

    static private $database = null;
    /* @var Mysqli $conn */
    private $conn = false;  //DB connection resources

    /**
     * Constructor, to connect to Database, select Database and set charset
     * @param $config string configuration array
     */
    private function __construct($config = array())
    {
        $host = isset($config['host']) ? $config['host'] : 'localhost';
        $user = isset($config['user']) ? $config['user'] : 'root';
        $password = isset($config['password']) ? $config['password'] : '';
        $dbname = isset($config['dbname']) ? $config['dbname'] : '';
//        $port = isset($config['port']) ? $config['port'] : '80';
        $charset = isset($config['charset']) ? $config['charset'] : 'utf8';


        if (!$this->conn = new Mysqli($host, $user, $password)) {
            throw new DatabaseException('database connection error', DatabaseErrorCodes::CONNECTION_ERROR);
        }

        if (!$this->conn->select_db($dbname)) {
            throw new DatabaseException('database selection error', DatabaseErrorCodes::DATABASE_SELECTION_ERROR);
        }

        $this->setChar($charset);

    }

    /**
     * Set charset
     * @access private
     * @param $charset string charset
     */
    private function setChar($charest)
    {
        $sql = 'SET NAMES ' . $charest;
        $this->query($sql);
    }

    /**
     * Execute SQL statement
     * @access public
     * @param $sql string SQL query statement
     * @return $result，if succeed, return resrouces; if fail return error message and exit
     */
    public function query($sql)
    {
//         Write SQL statement into log
//        $str = "[" . date("Y-m-d H:i:s") . "] --- " . $sql . PHP_EOL;
        //TODO log file helyét megcsinálni
//        file_put_contents("log.txt", $str,FILE_APPEND);

        $result = $this->conn->query($sql);

        return $result;
    }

    public static function Database($config = array())
    {
        if (self::$database == null) {
            self::$database = new Database($config);
        }
        return self::$database;
    }

    public function switchAutoCommit($bool)
    {
        return $this->conn->autocommit($bool);
    }

    public function commit()
    {
        return $this->conn->commit();
    }

    public function rollBack()
    {
        return $this->conn->rollback();
    }

    /**
     * Get the first column of the first record
     * @access public
     * @param $sql string SQL query statement
     * @return return the value of this column
     */
//    public function getOne($sql){
//        $result = $this->query($sql);
//        $row = mysqli_fetch_row($result);
//
//        if ($row) {
//            return $row[0];
//        } else {
//            return false;
//        }
//    }

    /**
     * Get last insert id
     */

    public function getInsertId()
    {
        return $this->conn->insert_id;
    }

    public function getAffectedRows()
    {
        return $this->conn->affected_rows;
    }

    /**
     * Get error number
     * @access private
     * @return int error number
     */

    public function errno()
    {
        return $this->conn->errno;
    }

    public function escapeString($string)
    {
        return $this->conn->real_escape_string($string);
    }

    /**
     * Get error message
     * @access private
     * @return int error message
     */
    public function error()
    {
        return $this->conn->error;
    }

}