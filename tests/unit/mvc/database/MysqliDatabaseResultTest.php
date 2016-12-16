<?php
/**
 * Created by PhpStorm.
 * User: Gumacs
 * Date: 2016-12-15
 * Time: 04:03 AM
 */

namespace Tests\Unit\Mvc\Database;


use Framework\Mvc\Database\MysqliDatabaseResult;
use Tests\Helpers\Models\TestModel;


class MysqliDatabaseResultTest extends \PHPUnit_Framework_TestCase
{
    /* @var \mysqli $database */
    private $database;

    public function setUp()
    {
        $this->database = new \mysqli('localhost:3306', 'test', 'test', 'test');
    }

    public function testFetchObjectArrayFalse(){
        $string = uniqid();
        $result = new MysqliDatabaseResult($this->database, $this->database->query("UPDATE test.test SET teststring='{$string}' WHERE idtest = 1"));

        $this->assertEquals(true, $result->isSuccess());

        $fetch = $result->fetchObjectArray(TestModel::class);
        foreach ($fetch as $obj){
            $this->assertTrue(false);
        }
        $this->assertEquals(0, sizeof($fetch));
    }

    public function testYieldObjectArrayFalse(){
        $string = uniqid();
        $result = new MysqliDatabaseResult($this->database, $this->database->query("UPDATE test.test SET teststring='{$string}' WHERE idtest = 1"));

        $this->assertEquals(true, $result->isSuccess());

        foreach ($result->yieldObjectArray(TestModel::class) as $obj){
            $this->assertNull($obj);
        }
    }

    public function testFetchObjectArrayTrue(){
        $result = new MysqliDatabaseResult($this->database, $this->database->query('SELECT * FROM test.test'));

        $this->assertEquals(true, $result->isSuccess());

        $fetch = $result->fetchObjectArray(TestModel::class);
        $this->assertEquals(5, sizeof($fetch));
        foreach ($fetch as $obj){
            if($obj instanceof TestModel){
                $this->assertTrue(true);
            }else{
                $this->assertTrue(false);
            }
        }
    }

    public function testYieldObjectArrayTrue(){
        $result = new MysqliDatabaseResult($this->database, $this->database->query('SELECT * FROM test.test'));

        $this->assertEquals(true, $result->isSuccess());

        foreach ($result->yieldObjectArray(TestModel::class) as $obj){
            if($obj instanceof TestModel){
                $this->assertTrue(true);
            }else{
                $this->assertTrue(false);
            }
        }
    }

    public function testGetAffectedRowsOne(){
        $string = uniqid();
        $result = new MysqliDatabaseResult($this->database, $this->database->query("UPDATE test.test SET teststring='{$string}' WHERE idtest = 1"));

        $this->assertEquals(true, $result->isSuccess());
        $this->assertEquals(1, $result->getAffectedRows());
    }

    public function testGetAffectedRowsZero(){
        $result = new MysqliDatabaseResult($this->database, $this->database->query('SELECT * FROM test.test'));

        $this->assertEquals(true, $result->isSuccess());
        $this->assertEquals(5, $result->getAffectedRows());
    }

    public function testConstructorSuccess(){
        $result = new MysqliDatabaseResult($this->database, $this->database->query('SELECT * FROM test.test'));

        $this->assertEquals(true, $result->isSuccess());
        $this->assertEquals(5, $result->getNumberOfRows());
    }

    public function testConstructorFailure(){
        $result = new MysqliDatabaseResult($this->database, $this->database->query('SELECT * FROM test.tesst'));

        $this->assertEquals(false, $result->isSuccess());
        $this->assertEquals(1, sizeof($result->getErrors()));
    }


}
