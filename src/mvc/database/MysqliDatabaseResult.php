<?php
/**
 * Created by PhpStorm.
 * User: Gumacs
 * Date: 2016-12-14
 * Time: 02:36 AM
 */

namespace Framework\Mvc\Database;


use Framework\Abstractions\Interfaces\IDatabaseResult;

class MysqliDatabaseResult implements IDatabaseResult
{
    /* @var \mysqli_result $result */
    private $result;
    private $success;
    private $errors;

    /**
     * DatabaseResult constructor.
     * @param \mysqli $connection
     * @param \mysqli_result|\mysqli_stmt|bool $result
     */
    public function __construct($connection, $result)
    {
        if (!($result instanceof \mysqli_result) && !($result instanceof \mysqli_stmt) && !is_bool($result)) {
            throw new \InvalidArgumentException('Fatal error: DatabaseResult expected mysqli_result or mysqli_stmt or bool, got ' . gettype($result));
        }
        if ($result instanceof \mysqli_result || is_bool($result)) {
            $this->success = true;
            if ($result !== false) {
                $this->result = [
                    'mysqli_result' => $result,
                    'affected_rows' => $connection->affected_rows,
                    'insert_id' => $connection->insert_id,
                    'num_rows' => isset($result->num_rows) ? $result->num_rows : 0
                ];
            } else {
                $this->success = false;
                $this->result = [
                    'mysqli_result' => false,
                    'affected_rows' => -1,
                    'insert_id' => 0,
                    'num_rows' => 0
                ];

            }
            $this->errors[$connection->connect_errno] = $connection->connect_error;
        } elseif ($result instanceof \mysqli_stmt) {
            if ($result->errno === 0) {
                $this->success = true;
                $result->store_result();
                $this->result = [
                    'mysqli_result' => $result->get_result(),
                    'affected_rows' => $result->affected_rows,
                    'insert_id' => $result->insert_id,
                    'num_rows' => $result->num_rows
                ];
                $this->errors[$result->errno] = $result->error;
                $result->close();
            } else {
                $this->success = false;
                $this->result = [
                    'mysqli_result' => false,
                    'affected_rows' => -1,
                    'insert_id' => 0,
                    'num_rows' => 0
                ];
                $this->errors[$result->errno] = $result->error;
            }
        }
    }

    public function isSuccess()
    {
        return $this->success;
    }

    public function getErrors()
    {
        return $this->errors;
    }

    public function getAffectedRows()
    {
        return $this->result['affected_rows'];
    }

    public function getInsertId()
    {
        return $this->result['insert_id'];
    }

    public function getNumberOfRows()
    {
        return $this->result['num_rows'];
    }

    public function yieldObjectArray($class_name)
    {
        if (!is_bool($this->result['mysqli_result'])) {
            while ($obj = $this->result['mysqli_result']->fetch_object($class_name)) {
                yield $obj;
            }
        }
    }

    public function fetchObjectArray($class_name)
    {
        if (!is_bool($this->result['mysqli_result'])) {

            $result = [];
            while ($obj = $this->result['mysqli_result']->fetch_object($class_name)) {
                $result[] = $obj;
            }
            return $result;
        } else {
            return [];
        }
    }

    public function yieldAssocArray()
    {
        if (!is_bool($this->result['mysqli_result'])) {
            while ($row = $this->result['mysqli_result']->fetch_assoc()) {
                yield $row;
            }
        }
    }

    public function fetchAssocArray()
    {
        if (!is_bool($this->result['mysqli_result'])) {
            $result = [];
            while ($row = $this->result['mysqli_result']->fetch_assoc()) {
                $result[] = $row;
            }
            return $result;
        } else {
            return [];
        }
    }

    public function yieldArray()
    {
        if (!is_bool($this->result['mysqli_result'])) {
            while ($row = $this->result['mysqli_result']->fetch_row()) {
                yield $row;
            }
        }
    }

    public function fetchArray()
    {
        if (!is_bool($this->result['mysqli_result'])) {

            $result = [];
            while ($row = $this->result['mysqli_result']->fetch_row()) {
                $result[] = $row;
            }
            return $result;
        } else {
            return [];
        }
    }

    public function __destruct()
    {
        if (!is_bool($this->result['mysqli_result'])) {
            $this->result['mysqli_result']->free();
        }
    }

}