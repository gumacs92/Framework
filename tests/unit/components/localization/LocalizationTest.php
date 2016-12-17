<?php
/**
 * Created by PhpStorm.
 * User: Gumacs
 * Date: 2016-12-09
 * Time: 02:24 PM
 */

namespace Tests\Unit\Components\Localization;


use Framework\Components\Localization\Localization;

class LocalizationTest extends \PHPUnit_Framework_TestCase
{
    public function setUp()
    {
        if (!defined('LOCALE_PATH')) {
            define('LOCALE_PATH', getcwd() . DS . 'tests' . DS . 'helpers' . DS . 'locale' . DS);
        }
    }

    public function testEnglishTranslation()
    {
//        $_REQUEST['lang'] = 'en';
//
//        $Localization = new Localization();
//        $Localization->init();
//        $Localization->dispatch();
//
//        $this->assertEquals('testEn', __('test'));
    }

    public function testHungarianTranslation()
    {
        $_REQUEST['lang'] = 'hu';

        $localization = new Localization();
        $localization->init();
        $localization->dispatch();

        $this->assertEquals('testHu', __('test'));
    }

}
