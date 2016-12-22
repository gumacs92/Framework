<?php
/**
 * Created by PhpStorm.
 * User: Gumacs
 * Date: 2016-12-15
 * Time: 04:50 AM
 */

namespace Tests\Unit\Mvc\Database;


use Framework\Mvc\Database\MysqliDatabase;


class MysqliDatabaseTest extends \PHPUnit_Framework_TestCase
{
    /* @var MysqliDatabase $database */
    private $database;

    public function setUp()
    {
        $this->database = MysqliDatabase::Database([
            'host' => 'localhost',
            'user' => 'test',
            'password' => 'test',
            'schema' => 'test',
            'port' => 3306
        ]);
    }

    public function testExecuteQueriesFailure(){
        $string = uniqid();
        $this->database->addQuery('SELECT * FROM test');
        $this->database->addQueryStatement("UPDATE test SET teststrsing=? WHERE idtest = ?", [$string, 3]);
        $this->database->addQueryStatement("SELECT * FROM test WHERE idstest = ? OR idtest = ?", [3, 4]);

        $this->assertEquals(false, $this->database->executeQueries());
        $this->assertEquals(2, sizeof($this->database->getAllErrors()));
        $this->assertEquals(1054, key(current($this->database->getAllErrors())));
    }

    public function testExecuteQueriesSuccess(){
        $string = uniqid();
        $this->database->addQuery('SELECT * FROM test');
        $this->database->addQueryStatement("UPDATE test SET teststring=? WHERE idtest = ?", [$string, 3]);
        $this->database->addQueryStatement("SELECT * FROM test WHERE idtest = ? OR idtest = ?", [3, 4]);

        $this->assertEquals(true, $this->database->executeQueries());
        $this->assertEquals(5, current($this->database->getAllNumberOfRows()));
        $affected_rows = $this->database->getAllAffectedRows();
        $this->assertEquals(5, current($affected_rows));
        $this->assertEquals(1, next($affected_rows));
        $this->assertEquals(2, next($affected_rows));
    }

    public function testExecuteQueryInsert(){
        $this->assertEquals(true, $this->database->executeQuery("INSERT INTO test (teststring, testint, testbool) VALUES ('asd1', 10, 1)"));
        $this->assertEquals(1, $this->database->getLastAffectedRows());
    }

    public function testExecuteQueryDelete(){
        $this->assertEquals(true, $this->database->executeQuery("DELETE FROM test WHERE teststring = 'asd1'"));
        $this->assertEquals(1, $this->database->getLastAffectedRows());
    }

    public function testExecuteQuerySelect(){
        $this->assertEquals(true, $this->database->executeQuery('SELECT * FROM test'));
        $this->assertEquals(5, $this->database->getLastNumberOfRows());
    }

    public function testExecuteQueryStatementFailure(){
        $string = uniqid();
        $this->assertEquals(false, $this->database->executeQueryStatement("UPDATE test SET testsstring=? WHERE idtest = ?", [$string, 3]));
        $this->assertEquals(1054, key($this->database->getLastErrors()));
    }

    public function testExecuteQueryStatementSuccess1(){
        $string = uniqid();
        $this->assertEquals(true, $this->database->executeQueryStatement("UPDATE test SET teststring=? WHERE idtest = ?", [$string, 3]));
        $this->assertEquals(1, $this->database->getLastAffectedRows());
    }

    public function testExecuteQueryStatementSuccess2(){
        $this->assertEquals(true, $this->database->executeQueryStatement("SELECT * FROM test WHERE idtest = ? OR idtest = ?", [3, 4]));
        $this->assertEquals(2, $this->database->getLastNumberOfRows());
    }
}
