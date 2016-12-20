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

    public function testWithParamsAction($params)
    {
        echo "Hello testWithParamsAction " . $params . "\n";
    }

    public function testWithIntParamsAction($int, $params)
    {
        echo "Hello testWithIntParamsAction " . $int . " " . $params[0] . " " . $params[1] . "\n";
    }
}