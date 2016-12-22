<?php
/**
 * Created by PhpStorm.
 * User: Gumacs
 * Date: 2016-12-15
 * Time: 09:47 PM
 */

namespace Tests\Unit\Mvc\Database;


use Framework\Core\ComponentHandler;
use Framework\Core\Core;
use Framework\Mvc\Database\DAL;
use Tests\Helpers\Models\TestModel;


class DALTest extends \PHPUnit_Framework_TestCase
{
    /* @var DAL $dal */
    private $dal;

    public function setUp()
    {
        Core::init(getcwd() . '\\tests\\Helpers\\config\\');

        $this->dal = new DAL('test');
    }

    public function testInsertSuccess(){

        $this->dal->insert(['teststring' => 'test1', 'testint' => 123, 'testbool' => 0]);

        $this->dal->select(['*'], []);

        $results = $this->dal->getLastResult(DAL::FETCH_RESULT, DAL::OBJECT_TYPE, TestModel::class);

        $this->assertEquals(6, sizeof($results));
    }

    public function testDeleteSuccess(){

        $this->dal->delete(['teststring' => 'test1']);

        $this->dal->select(['*'], []);

        $results = $this->dal->getLastResult(DAL::FETCH_RESULT, DAL::OBJECT_TYPE, TestModel::class);

        $this->assertEquals(5, sizeof($results));

    }
}
