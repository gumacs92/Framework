<?php
/**
 * Created by PhpStorm.
 * User: Gumacs
 * Date: 2016-12-15
 * Time: 04:35 AM
 */

namespace Tests\Helpers\Models;


use Framework\Mvc\Model\Model;

class TestModel extends Model
{
    private $idtest;
    private $teststring;
    private $testint;
    private $testbool;
    private $timestamp;

    public function __construct()
    {
    }

    /**
     * @return mixed
     */
    public function getIdtest()
    {
        return $this->idtest;
    }

    /**
     * @param mixed $idtest
     */
    public function setIdtest($idtest)
    {
        $this->idtest = $idtest;
    }

    /**
     * @return mixed
     */
    public function getTeststring()
    {
        return $this->teststring;
    }

    /**
     * @param mixed $teststring
     */
    public function setTeststring($teststring)
    {
        $this->teststring = $teststring;
    }

    /**
     * @return mixed
     */
    public function getTestint()
    {
        return $this->testint;
    }

    /**
     * @param mixed $testint
     */
    public function setTestint($testint)
    {
        $this->testint = $testint;
    }

    /**
     * @return mixed
     */
    public function getTestbool()
    {
        return $this->testbool;
    }

    /**
     * @param mixed $testbool
     */
    public function setTestbool($testbool)
    {
        $this->testbool = $testbool;
    }

    /**
     * @return mixed
     */
    public function getTimestamp()
    {
        return $this->timestamp;
    }

    /**
     * @param mixed $timestamp
     */
    public function setTimestamp($timestamp)
    {
        $this->timestamp = $timestamp;
    }


}