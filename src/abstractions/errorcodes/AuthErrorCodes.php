<?php
namespace Framework\Abstractions\Errorcodes;

use Framework\Abstractions\BaseEnum;

/**
 * Created by PhpStorm.
 * User: Gumacs
 * Date: 2016-11-14
 * Time: 05:12 PM
 */
abstract class AuthErrorCodes extends BaseEnum
{
    const NOT_VALID_SESSION = 0;
    const COULDNT_AUTHENTICATE = 1;
    const NOT_AUTHORIZED = 2;
    const NOT_IAUTH_CLASS = 3;
    const NO_AUTH_LEVEL = 4;
    const NOT_RIGHT_LEVEL = 5;
    const NO_SESSION_AVAILABLE = 6;
}