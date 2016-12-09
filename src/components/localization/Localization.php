<?php
/**
 * Created by PhpStorm.
 * User: Gumacs
 * Date: 2016-12-09
 * Time: 01:35 PM
 */

namespace Framework\Components\Localization;

use Framework\Abstractions\Interfaces\IComponent;
use Gettext\Translations;
use Gettext\Translator;

class Localization implements IComponent
{

    public function init($default = 'en')
    {
        if (isset($_SESSION['lang'])) {
            if (isset($_REQUEST['l'])) {
                $_SESSION['lang'] = $_REQUEST['l'];
                define("LANGUAGE", $_REQUEST['l']);
            } else {
                define("LANGUAGE", $_SESSION['lang']);
            }
        } else {
            $_SESSION['lang'] = isset($_REQUEST['l']) ? $_REQUEST['l'] : $default;
            define("LANGUAGE", isset($_REQUEST['l']) ? $_REQUEST['l'] : $default);
        };
    }

    public function dispatch()
    {
        //TODO multiplet ways to translate
        $translations = Translations::fromPoFile(LOCALE_PATH . LANGUAGE . DS . 'LC_MESSAGES' . DS . 'messages.po');
        /* @var Translations $translations */
        $translations->toPhpArrayFile(LOCALE_PATH . LANGUAGE . DS . 'LC_MESSAGES' . DS . LANGUAGE . '.php');

        $t = new Translator();
        $t->loadTranslations(LOCALE_PATH . LANGUAGE . DS . 'LC_MESSAGES' . DS . LANGUAGE . '.php');

        $t->register();
    }
}