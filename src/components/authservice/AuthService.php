<?php
namespace Framework\Core;

use Framework\Abstractions\Errorcodes\AuthErrorCodes;
use Framework\Abstractions\Exceptions\AuthServiceException;
use Framework\Abstractions\Interfaces\IAuth;
use Framework\Abstractions\Interfaces\IComponent;
use ReflectionClass;

/**
 * Created by PhpStorm.
 * User: Gumacs
 * Date: 2016-10-08
 * Time: 06:49 PM
 */



class AuthService implements IComponent
{
    private static $errorCode;
    private static $errorMessage;

    final static public function serve(){
//        try {
//            if (AuthService::checkAuth()) {
//                $this->viewModel->setAndShowView('search.php');
//            } else {
//                switch (AuthService::getErrorCode()) {
//                    case AuthErrorCodes::NO_SESSION_AVAILABLE:
//                        $this->redirect('/login', '');
//                        break;
//                    case AuthErrorCodes::NOT_RIGHT_LEVEL:
//                        $authlevel = AuthService::getSessionAuthLevel();
//                        $this->redirect('/' . $authlevel, '/camera');
//                        break;
//                    default:
//                        break;
//                }
//            }
//        } catch (AuthServiceException $exception) {
//            switch ($exception->getCode()) {
//                case AuthErrorCodes::NO_AUTH_LEVEL:
//                case AuthErrorCodes::NOT_VALID_SESSION:
//                default:
//                    $this->redirect('login/invalidsession', '');
//                    break;
//            }
//        }
    }

    final static public function checkAuth()
    {
        if (self::hasValidSession()) {
            if (self::hasSufficientAuthority()) {
                return true;
            }
        }
        return false;
    }

    final static public function hasValidSession()
    {
        if (self::isSessionAvailable()) {
            if (self::isValidSession()) {
                return true;
            } else {
                self::$errorCode = AuthErrorCodes::NOT_VALID_SESSION;
                self::$errorMessage = "The current session is not valid, and got unauthenticated!";
                self::invalidateSession();
                $exception = new AuthServiceException(self::$errorMessage, self::$errorCode);
                throw $exception;
            }
        } else {
            self::$errorCode = AuthErrorCodes::NO_SESSION_AVAILABLE;
            self::$errorMessage = "There is no available session!";
            return false;
        }
    }

    final static private function isSessionAvailable()
    {
        if (isset($_SESSION['LAST_ACTIVITY']) && ((time() - $_SESSION['LAST_ACTIVITY']) > $GLOBALS['config']['sessiontimeout'])) {
            self::invalidateSession();
            return false;
        }else if(!isset($_SESSION['LAST_ACTIVITY'])){
            return false;
        }else{
            $_SESSION['LAST_ACTIVITY'] = time();
            return true;
        }
    }

    /**
     *Invalidates the current session.
     */
    final static public function invalidateSession()
    {
        session_unset();
        session_destroy();
    }

    final static private function isValidSession()
    {
        if (isset($_SESSION['id_user']) && !empty($_SESSION['id_user'])
            && isset($_SESSION['auth_level']) && !empty($_SESSION['auth_level'])
        ) {
            return true;
        } else {
            return false;
        }
    }

    final static public function hasSufficientAuthority()
    {
        if (isset($_SESSION['auth_level'])) {
            //TODO manage the different level of authorities
//            $path = explode(DS, CURR_CONTROLLER_PATH);
//            $size = sizeof($path);
//            if ($path[$size - 2] == $_SESSION['auth_level']) {
                return true;
//            } else {
//                self::$errorCode = AuthErrorCodes::NOT_RIGHT_LEVEL;
//                self::$errorMessage = "The current level is not sufficient!";
//                return false;
//            }
        } else {
            self::$errorCode = AuthErrorCodes::NO_AUTH_LEVEL;
            self::$errorMessage = "The current session is not valid, and got unauthenticated!";
            self::invalidateSession();
            $exception = new AuthServiceException(self::$errorMessage, self::$errorCode);
            throw $exception;
        }
    }

    /* @var IAuth $usertoauth */
    final static public function setValidSession($usertoauth)
    {
        $reflection = new ReflectionClass($usertoauth);
        if ($reflection->implementsInterface('framework\Abstractions\Interfaces\IAuth')) {
            $id = $usertoauth->getUniqueId();
            $level = $usertoauth->getAuthLevel();
            if (!empty($id) && !empty($level)) {
                //TODO token regenerate stb.. stb..
                $_SESSION['id_user'] = $id;
                $_SESSION['auth_level'] = $level;
                $_SESSION['LAST_ACTIVITY'] = time();
                return true;
            } else {
                self::$errorCode = AuthErrorCodes::COULDNT_AUTHENTICATE;
                self::$errorMessage = "Couldn't authenticate the user!";
                self::invalidateSession();
                return false;
            }
        } else {
            self::$errorCode = AuthErrorCodes::NOT_IAUTH_CLASS;
            self::$errorMessage = "The object doesn't implement the IAuth interface!";
            self::invalidateSession();
            $exception = new AuthServiceException(self::$errorMessage, self::$errorCode);
            throw $exception;
        }

//        $this->user = $this->getUserByLogin($authdata['username'], $authdata['password']);
    }

    final static public function getSessionAuthLevel(){
        if(isset($_SESSION['auth_level'])){
            return $_SESSION['auth_level'];
        } else {
            return false;
        }
    }

    /**
     * @return mixed
     */
    final static public function getErrorCode()
    {
        return self::$errorCode;
    }

    /**
     * @return mixed
     */
    final static public function getErrorMessage()
    {
        return self::$errorMessage;
    }

    public function init()
    {
        // TODO: Implement init() method.
    }

    public function dispatch()
    {
        // TODO: Implement dispatch() method.
    }
}