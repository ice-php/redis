<?php
declare(strict_types=1);

namespace icePHP;

class RedisException extends \Exception
{
    //数值增减方法的参数错误,不允许为0
    const PARAM_ERROR_FOR_CREASE=5;
}