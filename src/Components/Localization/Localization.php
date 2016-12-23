<?php
/**
 * Created by PhpStorm.
 * User: Gumacs
 * Date: 2016-12-09
 * Time: 01:35 PM
 */

namespace Framework\Components\Localization;

use Framework\Components\AbstractComponent;
use Gettext\Translations;
use Gettext\Translator;

class Localization extends AbstractComponent
{

    public function init($default = 'en')
    {
        if (isset($_SESSION['lang'])) {
            if (isset($_REQUEST['lang'])) {
                $_SESSION['lang'] = $_REQUEST['lang'];
                define("LANGUAGE", $_REQUEST['lang']);
            } else {
                define("LANGUAGE", $_SESSION['lang']);
            }
        } else {
            $_SESSION['lang'] = isset($_REQUEST['lang']) ? $_REQUEST['lang'] : $default;
            define("LANGUAGE", isset($_REQUEST['lang']) ? $_REQUEST['lang'] : $default);
        };
    }

    public function start()
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