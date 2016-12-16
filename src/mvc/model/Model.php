<?php
namespace Framework\Mvc\Model;

// framework/core/Model.class.php

// Base Model Class

use Framework\Abstractions\Errorcodes\DatabaseErrorCodes;
use Framework\Abstractions\Exceptions\DatabaseException;
use Framework\Mvc\Database\Database;
use ReflectionClass;
use ReflectionProperty;

abstract class Model
{
    protected $database;
    protected $table; //table name
    protected $fields = array();  //fields list
    protected $primaryfield = '';

    public function __construct()
    {
        $dbconfig['host'] = $GLOBALS['config']['host'];
        $dbconfig['user'] = $GLOBALS['config']['user'];
        $dbconfig['password'] = $GLOBALS['config']['password'];
        $dbconfig['dbname'] = $GLOBALS['config']['dbname'];
        $dbconfig['port'] = $GLOBALS['config']['port'];
        $dbconfig['charset'] = $GLOBALS['config']['charset'];

        $namespace = explode("\\", get_class($this));
        $classname = $namespace[sizeof($namespace) - 1];
        $table = lcfirst(substr($classname, 0, -5));

        $this->database = Database::Database($dbconfig);
        $this->table = $GLOBALS['config']['prefix'] . $table;
        $this->getFields();
    }

    /**
     * Get the list of table fields
     *
     */
    private function getFields()
    {
        $sql = "DESC $this->table";

        $result = $this->database->query($sql);

        if ($result) {
            $description = $result->fetch_all(MYSQLI_ASSOC);

            foreach ($description as $row) {
                $this->fields[] = $row['Field'];
                if ($row['Key'] == 'PRI') {
                    $this->primaryfield = $row['Field'];
                }
            }
        } else {
            throw new DatabaseException($this->database->errno() . ": " . $this->database->error(), DatabaseErrorCodes::QUERY_FAILURE);
        }
    }

    public function selectFirstFromAll($class)
    {
        $result = $this->selectAll($class);
        $row = $result[0];
        return $row;
    }

    public function selectAll($class)
    {
        $sql = "SELECT * FROM $this->table";
        $result = $this->database->query($sql);

        if ($result) {
            $list = array();

            while ($row = $result->fetch_object($class)) {
                $list[] = $row;
            }

            return $list;
        } else {
            throw new DatabaseException($this->database->errno() . ": " . $this->database->error(), DatabaseErrorCodes::QUERY_FAILURE);
        }

    }

    /**
     * @param $obj
     * @param null $exceptionprop
     * @return bool|int|string
     */
    public function insert($obj, $exceptionprop = null)
    {
        $field_list = '';  //field list string
        $value_list = '';  //value list string

        $reflection = new ReflectionClass($obj);
        $props = $reflection->getProperties(ReflectionProperty::IS_PRIVATE);

        //TODO do it more common
        if (empty($props)) {
            $traits = $reflection->getTraits();
            foreach ($traits as $trait) {
                $props = $trait->getProperties();
                if (!empty($props))
                    break;
            }
        }


        foreach ($props as $prop) {
            $fieldname = $prop->getName();
            $prop->setAccessible(true);
            $fieldvalue = $prop->getValue($obj);
            if (!in_array($fieldname, $exceptionprop) && in_array($fieldname, $this->fields)) {
                $field_list .= $fieldname . ",";
                if (is_null($fieldvalue)) {
                    $value_list .= "null,";
                } else {
                    $value_list .= is_numeric($fieldvalue) ? $fieldvalue . "," : "'" . $fieldvalue . "',";
                }
            }
        }

        // Trim the comma on the right
        $field_list = rtrim($field_list, ',');
        $value_list = rtrim($value_list, ',');

        // Construct sql statement
        $sql = "INSERT INTO $this->table ($field_list) VALUES ($value_list)";

        $result = $this->database->query($sql);

        if ($result) {
            return $this->database->getInsertId();
        } else {
            throw new DatabaseException($this->database->errno() . ": " . $this->database->error(), DatabaseErrorCodes::QUERY_FAILURE);
        }

    }


    /**
     * @param $obj
     * @param null $exceptionprop
     * @return bool|int
     */
    public function update($obj, $exceptionprop = null)
    {
        $updatelist = ''; //update fields
        $where = 0;   //update condition, default is 0

        $reflection = new ReflectionClass($obj);
        $props = $reflection->getProperties(ReflectionProperty::IS_PRIVATE);

        //TODO do it more common
        if (empty($props)) {
            $traits = $reflection->getTraits();
            foreach ($traits as $trait) {
                $props = $trait->getProperties();
                if (!empty($props))
                    break;
            }
        }

        foreach ($props as $prop) {
            /* @var ReflectionProperty $prop */
            $fieldname = $prop->getName();
            $prop->setAccessible(true);
            $fieldvalue = $prop->getValue($obj);
            if (!in_array($fieldname, $exceptionprop) && in_array($fieldname, $this->fields)) {
                if ($this->primaryfield == $fieldname) {
                    $where = "$fieldname=$fieldvalue";
                } else {
                    if (is_null($fieldvalue)) {
                        $updatelist .= "$fieldname=null,";
                    } else {
                        $updatelist .= "$fieldname=" . (is_numeric($fieldvalue) ? "$fieldvalue," : "'$fieldvalue',");
                    }
                }

            }
        }

        // Trim comma on the right of update list
        $updatelist = rtrim($updatelist, ',');

        // Construct SQL statement
        $sql = "UPDATE $this->table SET $updatelist WHERE $where";
        $result = $this->database->query($sql);

        if ($result) {
            $rows = $this->database->getAffectedRows();
            if ($rows) {
                return $rows;
            } else {
                return false;
            }
        } else {
            throw new DatabaseException($this->database->errno() . ": " . $this->database->error(), DatabaseErrorCodes::QUERY_FAILURE);
        }


    }

    /**
     * Delete records
     * @access public
     * @param $pk mixed could be an int or an array
     * @return mixed If succeed, return the count of deleted records, if fail, return false
     */

    public function delete($pk)
    {
        $where = 0; //condition string

        //Check if $pk is a single value or array, and construct where condition accordingly
        if (is_array($pk)) {
            // array
            $where = "$this->primaryfield in (" . implode(',', $pk) . ")";
        } else {
            // single value
            $where = "$this->primaryfield = $pk";
        }

        // Construct SQL statement
        $sql = "DELETE FROM $this->table WHERE $where";

        $result = $this->database->query($sql);

        if ($result) {
            $rows = $this->database->getAffectedRows();
            if ($rows) {
                return $rows;
            } else {
                return false;
            }
        } else {
            throw new DatabaseException($this->database->errno() . ": " . $this->database->error(), DatabaseErrorCodes::QUERY_FAILURE);
        }


    }

    /**
     * Get info based on PK
     * @param $pk int Primary Key
     * @return array an array of single record
     */

//    public function selectByPk($pk)
//    {
//        $sql = "SELECT * FROM $this->table WHERE $this->primaryfield = $pk";
//        return $this->database->getRow($sql);
//
//    }

    /**
     * Get the count of all records
     *
     */

//    public function total()
//    {
//        $sql = "SELECT COUNT(*) FROM $this->table";
//        return $this->database->getOne($sql);
//    }

    /**
     * Get info of pagination
     * @param $offset int offset value
     * @param $limit int number of records of each fetch
     * @param $where string where condition,default is empty
     */

//    public function pageRows($offset, $limit, $where = '')
//    {
//        if (empty($where)) {
//            $sql = "SELECT * FROM $this->table LIMIT $offset, $limit";
//        } else {
//            $sql = "SELECT * FROM $this->table WHERE $where LIMIT $offset, $limit";
//        }
//        return $this->database->getAll($sql);
//    }

    /**
     * @return mixed
     */
    public function getDatabase()
    {
        return $this->database;
    }

}