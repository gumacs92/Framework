<?php

namespace Framework\Mvc\Database;

use Framework\Abstractions\Errorcodes\DatabaseErrorCodes;
use Framework\Abstractions\Exceptions\DatabaseException;
use Framework\Abstractions\Interfaces\IDatabase;
use mysqli;


/**
 *================================================================
 *framework/database/Mysql.class.php
 *database operation class
 *================================================================
 */
class MysqliDatabase implements IDatabase
{

    static private $database = null;
    /* @var Mysqli $conn */
    private $conn = false;  //DB connection resources

    private $queryList;
    private $resultList;
    /* @var MysqliDatabaseResult $lastResult */
    private $lastResult;

    private $autoCommit;


    private function __construct($config = array())
    {
        $host = isset($config['host']) ? $config['host'] : 'localhost';
        $user = isset($config['user']) ? $config['user'] : 'root';
        $password = isset($config['password']) ? $config['password'] : '';
        $schema = isset($config['schema']) ? $config['schema'] : '';
        $port = isset($config['port']) ? $config['port'] : '80';
        $charset = isset($config['charset']) ? $config['charset'] : 'utf8';


        if (!$this->conn = new Mysqli($host, $user, $password, $schema, $port)) {
            throw new DatabaseException('Fatal Error: Couldn\'t connect to database', DatabaseErrorCodes::CONNECTION_ERROR);
        }

        $this->autoCommit = true;

        $this->setChar($charset);
    }


    public static function Database($config = array())
    {
        if (self::$database == null) {
            self::$database = new MysqliDatabase($config);
        }
        return self::$database;
    }


    public function switchAutoCommit($bool)
    {
        $this->autoCommit = $bool;
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

    public function executeQuery($query)
    {
        $this->lastResult = new MysqliDatabaseResult($this->conn, $this->conn->query($query));
        return $this->lastResult->isSuccess();
    }

    public function executeQueryStatement($query, $params)
    {
        $stmt = $this->conn->stmt_init();
        if ($stmt->prepare($query)) {
            $binds = '';
            foreach ($params as $param) {
                if (is_string($param)) {
                    $binds .= 's';
                }
                if (is_double($param)) {
                    $binds .= 'd';
                }
                if (is_int($param) || is_null($param)) {
                    $binds .= 'i';
                }
            }

            if (!$stmt->bind_param($binds, ...$params)) {
                $this->lastResult = new MysqliDatabaseResult($this->conn, $stmt);
                return $this->lastResult->isSuccess();
            }

            if (!$stmt->execute()) {
                $this->lastResult = new MysqliDatabaseResult($this->conn, $stmt);
                return $this->lastResult->isSuccess();
            }
        }

        $this->lastResult = new MysqliDatabaseResult($this->conn, $stmt);
        return $this->lastResult->isSuccess();

    }

    public function executeQueries()
    {
        //Write SQL statement into log
        //$str = "[" . date("Y-m-d H:i:s") . "] --- " . $sql . PHP_EOL;
        //TODO logging
        //file_put_contents("log.txt", $str,FILE_APPEND);

        $this->resultList = [];

        if(empty($this->queryList)){
            return true;
        }

        foreach ($this->queryList as $id => $query) {
            if (is_array($query)) {
                $sql = $query['query'];
                $params = $query['params'];

                $this->executeQueryStatement($sql, $params);
                $this->resultList[$id] = $this->lastResult;
            } else {
                $this->executeQuery($query);
                $this->resultList[$id] = $this->lastResult;
            }
        }

        $this->queryList = [];

        /* @var MysqliDatabaseResult $result */
        foreach ($this->resultList as $result) {
            if (!$result->isSuccess()) {
                return false;
            }
        }

        return true;
    }

    public function addQuery($query)
    {
        $queryid = explode(' ', $query)[0] . '_' . uniqid();
        $this->queryList[$queryid] = $query;
    }

    //TODO a function which accepts blobs
    public function addQueryStatement($query, $params)
    {
        $queryid = explode(' ', $query)[0] . '_' . uniqid();
        $this->queryList[$queryid] = ['query' => $query, 'params' => $params];
    }

    private function setChar($charset)
    {
        $query = 'SET NAMES ' . $charset;
        return $this->executeQuery($query);
    }

    public function escapeString($string)
    {
        return $this->conn->real_escape_string($string);
    }

    public function getLastResult()
    {
        return $this->lastResult;
    }

    public function getLastInsertId()
    {
        return $this->lastResult->getInsertId();
    }

    public function getLastAffectedRows()
    {
        return $this->lastResult->getAffectedRows();
    }

    public function getLastNumberOfRows()
    {
        return $this->lastResult->getNumberOfRows();
    }

    public function getLastErrors()
    {
        return $this->lastResult->getErrors();
    }

    public function getAllResult()
    {
        return $this->resultList;
    }

    public function getAllInsertId()
    {
        $id_list = [];
        /* @var MysqliDatabaseResult $result */
        foreach ($this->resultList as $id => $result) {
            $insert_id = $result->getInsertId();
            if (!empty($insert_id)) {
                $id_list[$id] = $insert_id;
            }
        }

        return $id_list;
    }

    public function getAllAffectedRows()
    {
        $affected_list = [];
        /* @var MysqliDatabaseResult $result */
        foreach ($this->resultList as $id => $result) {
            $affected_rows = $result->getAffectedRows();
            if ($affected_rows !== -1) {
                $affected_list[$id] = $affected_rows;
            }
        }

        return $affected_list;
    }

    public function getAllNumberOfRows()
    {
        $numbers_list = [];
        /* @var MysqliDatabaseResult $result */
        foreach ($this->resultList as $id => $result) {
            $number = $result->getNumberOfRows();
            if (!empty($number)) {
                $numbers_list[$id] = $number;
            }
        }

        return $numbers_list;
    }

    public function getAllErrors()
    {
        $errors = [];
        /* @var MysqliDatabaseResult $result */
        foreach ($this->resultList as $id => $result) {
            $error =  $result->getErrors();
            if(key($error) !== 0){
                $errors[$id] = $error;
            }

        }

        return $errors;
    }

    public function __destruct()
    {
        $this->conn->close();
    }
}