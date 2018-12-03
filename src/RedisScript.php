<?php
/**
 * Redis脚本
 * User: 蓝冰大侠
 * Date: 2018/11/30
 * Time: 16:19
 */

namespace icePHP;


class RedisScript
{
    /**
     * @var \redis
     */
    protected $handle;

    /**
     * 构造一个脚本类
     * @param \redis $redis 主Redis句柄
     */
    public function __construct(\redis $redis)
    {
        $this->handle = $redis;
    }

    /**
     * 执行一段脚本
     * @param string $script 脚本
     * @param array $keys 键(可在脚本中使用KEYS[1],KEYS[2]访问 ,从1开始
     * @param array $argv 参数(可在脚本中使用ARGV[1],ARGV[2]访问 ,从1开始
     * @return mixed
     */
    public function exec(string $script, array $keys = [], array $argv = [])
    {
        return $this->handle->eval($script, array_merge($keys, $argv), count($keys));
    }

    /**
     * 将脚本存储到服务器上,并不执行
     * @param string $script
     * @return string 返回脚本名称(一段SHA值)
     */
    public function save(string $script): string
    {
        return $this->handle->script('load', $script);
    }

    /**
     * 执行一段已经存储到服务器上的脚本
     * @param string $name 之前得到的脚本名称(由save存储)
     * @param array $keys 键(可在脚本中使用KEYS[1],KEYS[2]访问 ,从1开始
     * @param array $argv 参数(可在脚本中使用ARGV[1],ARGV[2]访问 ,从1开始
     * @return mixed
     */
    public function execByName(string $name, array $keys = [], array $argv = [])
    {
        return $this->handle->evalSha($name, array_merge($keys, $argv), count($keys));
    }

    /**
     * 查看在服务器上是否存在指定名称的脚本
     * @param string $name
     * @return bool
     */
    public function exists(string $name): bool
    {
        return $this->handle->script('exists', $name);
    }

}