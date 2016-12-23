<?php
/**
 * Created by PhpStorm.
 * User: Gumacs
 * Date: 2016-12-08
 * Time: 06:11 PM
 */

namespace Tests\Helpers\Controllers;


class TestController
{
    public function testAction()
    {
        echo "Hello testAction\n";
    }

    public function testWithIntAction($int)
    {
        echo "Hello testWithIntAction " . $int . "\n";
    }

    public function testWithParamsAction($param)
    {
        echo "Hello testWithParamsAction " . $param . "\n";
    }

    public function testWithIntParamsAction($int, $param1, $param2)
    {
        echo "Hello testWithIntParamsAction " . $int . " " . $param1 . " " . $param2 . "\n";
    }
}