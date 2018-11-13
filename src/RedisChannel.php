<?php
declare(strict_types=1);

namespace icePHP;


/**
 * 频道
 */
final class RedisChannel
{
    /**
     * @var \redis
     */
    protected $handle;

    /**
     * @var string
     */
    protected $key;

    /**
     * 构造一个字符串类
     * @param \redis $redis 主Redis句柄
     * @param $key string 键
     */
    public function __construct(\redis $redis, string $key)
    {
        $this->handle = $redis;
        $this->key = $key;
    }

    /**
     * 发布消息到当前频道
     * @param $message string 消息
     * @return int 接收到信息 message 的订阅者数量。
     */
    public function publish(string $message):int
    {
        return $this->handle->publish($this->key, $message);
    }

    /**
     * 订阅本频道消息
     * @param callable $func 有消息时的回调函数
     * @return $this
     */
    public function subscribe(callable $func):self
    {
        $this->handle->subscribe([$this->key], $func);
        return $this;
    }
}