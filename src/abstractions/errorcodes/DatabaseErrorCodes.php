<?php
/**
 * Created by PhpStorm.
 * User: Gumacs
 * Date: 2016-11-14
 * Time: 05:20 PM
 */

namespace Framework\Abstractions\Errorcodes;

use Framework\Abstractions\BaseEnum;

class DatabaseErrorCodes extends BaseEnum
{
    const CONNECTION_ERROR = 0;
    const DATABASE_SELECTION_ERROR = 1;
    const COMMIT_ERROR = 2;
    const QUERY_FAILURE = 3;
}