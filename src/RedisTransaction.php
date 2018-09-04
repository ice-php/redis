<?php

declare(strict_types=1);

namespace icePHP;
/**
 * 事务类
 */
final class RedisTransaction
{
    /**
     * @var Redis
     */
    private $handle;

    /**
     * 实例化
     * @param \redis $redis 主Redis句柄
     */
    public function __construct(\redis $redis)
    {
        $this->handle = $redis;
    }

    /**
     * 开始一个事务
     */
    public function multi(): void
    {
        $this->handle->multi();
    }

    /**
     * 开始一个事务,multi的别名
     */
    public function begin(): void
    {
        self::multi();
    }

    /**
     * 提交一个事务,exec的别名
     */
    public function commit(): void
    {
        self::exec();
    }

    /**
     * 提交一个事务
     */
    public function exec(): void
    {
        $this->handle->exec();
    }

    /**
     * 回滚一个事务
     */
    public function discard(): void
    {
        $this->handle->discard();
    }

    /**
     * 回滚一个事务,discard的别名
     */
    public function rollback(): void
    {
        self::discard();
    }

    /**
     * 监视一个(或多个) key ，如果在事务执行之前这个(或这些) key 被其他命令所改动，那么事务将被打断。
     * @param $keys string|array
     */
    public function watch($keys): void
    {
        $this->handle->watch($keys);
    }

    /**
     * 取消 WATCH 命令对所有 key 的监视。
     */
    public function unwatch(): void
    {
        $this->handle->unwatch();
    }
}
