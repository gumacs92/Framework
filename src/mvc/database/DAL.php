<?php
/**
 * Created by PhpStorm.
 * User: Gumacs
 * Date: 2016-12-14
 * Time: 04:28 AM
 */

namespace Framework\Mvc\Database;


use Framework\Abstractions\Exceptions\DatabaseException;
use Framework\Abstractions\Interfaces\IDatabase;

class DAL
{
    const YIELD_RESULT = 0;
    const FETCH_RESULT = 1;
    const OBJECT_TYPE = 2;
    const ASSOC_TYPE = 3;
    const ARRAY_TYPE = 4;


    /* @var IDatabase $database */
    private $database;
    private $table;
    private $autocommit;

    public function __construct($table, $database_handler = '\\Framework\\Mvc\\Database\\MysqliDatabase')
    {
        $this->table = $table;

        $config = parse_ini_file(CONFIG_PATH . 'config.ini', true);

        if (class_exists($database_handler, true) && in_array('Framework\\Abstractions\\Interfaces\\IDatabase', class_implements($database_handler, true))) {
            $this->database = $database_handler::Database($config['Database']);
        } else {
            throw new DatabaseException("Fatal error: The database {$database_handler} doesn't exist");
        }
        $this->autocommit(true);
    }

    public function insert($params)
    {
        $paramsize = sizeof($params);
        $columns = array_keys($params);
        $values = array_values($params);
        $columnsstring = '';
        $valuesstring = '';
        for ($i = 0; $i < $paramsize; $i++) {
            $columnsstring .= $columns[$i] . ',';
            $value = $values[$i];
            if (is_string($value)) {
                $valuesstring .= "'" . $this->database->escapeString($value) . "',";
            } else {
                $valuesstring .= $values[$i] . ',';
            }
        }
        $columnsstring = rtrim($columnsstring, ',');
        $valuesstring = rtrim($valuesstring, ',');

        $sql = "INSERT INTO {$this->table} (" . $columnsstring . ") VALUES (" . $valuesstring . ")";

        if ($this->autocommit) {
            $this->database->executeQuery($sql);
        } else {
            $this->database->addQuery($sql);
        }
    }

    public function select($select, $where)
    {
        $selectstring = '';
        $wherestring = '';

        foreach ($select as $s) {
            $selectstring .= $s . ',';
        }

        foreach ($where as $col => $cond) {
            if (is_string($cond)) {
                $wherestring .= "$col LIKE '{$this->database->escapeString($cond)}',";
            } else {
                $wherestring .= "$col = $cond,";
            }
        }

        $selectstring = rtrim($selectstring, ',');
        $wherestring = rtrim($wherestring, ',');

        $sql = "SELECT {$selectstring} FROM {$this->table}" . (!empty($wherestring) ? " WHERE {$wherestring}" : "");

        if ($this->autocommit) {
            $this->database->executeQuery($sql);
        } else {
            $this->database->addQuery($sql);
        }
    }

    public function update($set, $where)
    {
        $setstring = '';
        $wherestring = '';

        foreach ($set as $col => $cond) {
            if (is_string($cond)) {
                $setstring .= "$col = '{$this->database->escapeString($cond)}',";
            } else {
                $setstring .= "$col = $cond,";
            }
        }

        foreach ($where as $col => $cond) {
            if (is_string($cond)) {
                $wherestring .= "$col LIKE '{$this->database->escapeString($cond)}',";
            } else {
                $wherestring .= "$col = $cond,";
            }
        }

        $setstring = rtrim($setstring, ',');
        $wherestring = rtrim($wherestring, ',');

        $sql = "UPDATE {$this->table} SET {$setstring}" . (!empty($wherestring) ? " WHERE {$wherestring}" : "");

        if ($this->autocommit) {
            $this->database->executeQuery($sql);
        } else {
            $this->database->addQuery($sql);
        }
    }

    public function delete($where)
    {
        $wherestring = '';

        foreach ($where as $col => $cond) {
            if (is_string($cond)) {
                $wherestring .= "$col LIKE '{$this->database->escapeString($cond)}',";
            } else {
                $wherestring .= "$col = $cond,";
            }
        }

        $wherestring = rtrim($wherestring, ',');

        $sql = "DELETE FROM {$this->table}" . (!empty($wherestring) ? " WHERE {$wherestring}" : "");

        if ($this->autocommit) {
            $this->database->executeQuery($sql);
        } else {
            $this->database->addQuery($sql);
        }
    }

    public function query($sql, $params)
    {
        $pattern = '/{{p}}/';
        preg_match($pattern, $sql, $matches);

        if (sizeof($matches) !== sizeof($params)) {
            throw new DatabaseException("Error: the number of {$pattern} patterns and parameters differ");
        }

        if ($this->autocommit) {
            $this->database->executeQueryStatement($sql, $params);
        } else {
            $this->database->addQueryStatement($sql, $params);
        }
    }

    //TODO autorevert
    public function autoCommit($switch)
    {
        $this->autocommit = $switch;
        $this->database->switchAutoCommit($switch);
    }

    public function commit()
    {
        $this->database->executeQueries();
        $this->database->commit();
    }

    public function rollBack()
    {
        $this->database->rollBack();
    }

    public function getInsertIds()
    {
        $this->database->getAllInsertId();
    }

    public function getLastInsertId()
    {
        $this->database->getLastInsertId();
    }

    public function getAffectedRows()
    {
        $this->database->getAllInsertId();
    }

    public function getLastAffectedRow()
    {
        $this->database->getLastAffectedRows();
    }

    public function getLastResult($mode = DAL::FETCH_RESULT, $type = DAL::ARRAY_TYPE, $class_name = '')
    {
        $method = '';
        switch ($mode) {
            case DAL::FETCH_RESULT:
                $method .= 'fetch';
                break;
            case DAL::YIELD_RESULT:
                $method .= 'yield';
                break;
        }
        switch ($type) {
            case DAL::ARRAY_TYPE:
                $method .= 'Array';
                break;
            case DAL::OBJECT_TYPE:
                $method .= 'ObjectArray';
                break;
            case DAL::ASSOC_TYPE:
                $method .= 'AssocArray';
                break;
        }
        $result = $this->database->getLastResult();
        if ($result->isSuccess()) {
            if ($type === DAL::OBJECT_TYPE) {
                return $this->database->getLastResult()->$method($class_name);
            } else {
                return $this->database->getLastResult()->$method();
            }
        } else {
            [];
        }

    }

    public function getErrors()
    {
        $this->database->getAllErrors();
    }

    public function getLastErrors()
    {
        $this->database->getLastErrors();
    }
}