<?php
declare(strict_types=1);

namespace icePHP;

class RedisException extends \Exception
{
    //无法读取Redis配置文件
    const MISS_CONFIG=1;

    //无法连接Redis服务器
    const CONNECT_FAIL=2;

    //Redis服务器身份验证失败
    const AUTH_FAIL=3;

    //不识别的Redis数据类型
    const TYPE_UNKNOWN=4;

    //数值增减方法的参数错误
    const PARAM_ERROR_FOR_CREASE=5;
}