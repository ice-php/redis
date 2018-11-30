<?php

declare(strict_types=1);

namespace icePHP;
/**
 * 服务器类
 */
final class RedisServer
{
    /**
     * @var \redis
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
     * 同步保存当前数据库的数据到磁盘。
     * 命令执行一个同步保存操作，将当前 Redis 实例的所有数据快照(snapshot)以 RDB 文件的形式保存到硬盘。
     * @internal
     * @return bool
     */
    public function save(): bool
    {
        return $this->handle->save();
    }

    /**
     * 在后台异步保存当前数据库的数据到磁盘。
     * 在后台异步(Asynchronously)保存当前数据库的数据到磁盘。
     * BGSAVE 命令执行之后立即返回 OK ，然后 Redis fork 出一个新子进程，原来的 Redis 进程(父进程)继续处理客户端请求，而子进程则负责将数据保存到磁盘，然后退出。
     * @internal
     * @return bool
     */
    public function saveAsync(): bool
    {
        return $this->handle->bgsave();
    }

    /**
     * 异步(Asynchronously)重写 AOF 文件以反应当前数据库的状态。
     * 执行一个 AOF文件 重写操作。重写会创建一个当前 AOF 文件的体积优化版本。
     * 即使 BGREWRITEAOF 执行失败，也不会有任何数据丢失，因为旧的 AOF 文件在 BGREWRITEAOF 成功之前不会被修改。
     * 重写操作只会在没有其他持久化工作在后台执行时被触发，也就是说：
     * 如果 Redis 的子进程正在执行快照的保存工作，那么 AOF 重写的操作会被预定(scheduled)，等到保存工作完成之后再执行 AOF 重写。在这种情况下， BGREWRITEAOF 的返回值仍然是 OK ，但还会加上一条额外的信息，说明 BGREWRITEAOF 要等到保存操作完成之后才能执行。在 Redis 2.6 或以上的版本，可以使用 INFO 命令查看 BGREWRITEAOF 是否被预定。
     * 如果已经有别的 AOF 文件重写在执行，那么 BGREWRITEAOF 返回一个错误，并且这个新的 BGREWRITEAOF 请求也不会被预定到下次执行。
     * 从 Redis 2.4 开始， AOF 重写由 Redis 自行触发， BGREWRITEAOF 仅仅用于手动触发重写操作。
     * @internal
     * @return bool
     */
    public function saveAOF(): bool
    {
        return $this->handle->bgrewriteaof();
    }

    /**
     * 返回最近一次 Redis 成功执行保存操作的时间点( SAVE 、 BGSAVE 等)，以 UNIX 时间戳格式表示。
     * @internal
     * @return int
     */
    public function lastSave(): int
    {
        return $this->handle->lastSave();
    }

    /**
     * 清空整个 Redis 服务器的数据(删除所有数据库的所有 key)。
     * @internal
     * @return bool
     */
    public function flushAll(): bool
    {
        return $this->handle->flushAll();
    }

    /**
     * 清空当前数据库中的所有 key 。
     * @internal
     * @return bool
     */
    public function flushDB(): bool
    {
        return $this->handle->flushDB();
    }

    /**
     * 返回关于 Redis 服务器的各种信息和统计值。
     * @return string
     */
    public function info(): string
    {
        return $this->handle->info();
    }

    /**
     * 获取一定数量的慢日志
     * @param int $count
     * @return mixed
     */
    public function slowLogGet(?int $count = null)
    {
        if ($count) {
            return $this->handle->slowlog("get $count");
        }
        return $this->handle->slowlog('get');
    }

    /**
     * 重置慢日志
     * @return mixed
     */
    public function slowLogReset()
    {
        return $this->handle->slowlog('reset');
    }

    /**
     * 获取当前慢日志的条数
     * @return mixed
     */
    public function slowLogLen()
    {
        return $this->handle->slowlog('len');
    }

    /**
     * 获取配置信息
     * @param $key string 配置项名称 或 匹配模式(*)
     * @return array
     */
    public function configGet($key = '*'): array
    {
        //这个参数就是这个样子的,只有两个
        return $this->handle->config('GET', $key, null);
    }

    /**
     * 设置配置信息
     * @param $key string  配置项名称
     * @param $value string  配置值
     * @return array
     */
    public function configSet(string $key, string $value): array
    {
        return $this->handle->config('SET', $key, $value);
    }

    /**
     * 返回所有连接到服务器的客户端信息和统计数据。
     * @return array
     *   [addr] => 127.0.0.1:52313
     * [fd] => 184
     * [idle] => 0
     * [flags] => N
     * [db] => 0
     * [sub] => 0
     * [psub] => 0
     * [qbuf] => 0
     * [obl] => 0
     * [oll] => 0
     * [events] => r
     * [cmd] => client
     *      addr ： 客户端的地址和端口
     * fd ： 套接字所使用的文件描述符
     * age ： 以秒计算的已连接时长
     * idle ： 以秒计算的空闲时长
     * flags ： 客户端 flag
     * db ： 该客户端正在使用的数据库 ID
     * sub ： 已订阅频道的数量
     * psub ： 已订阅模式的数量
     * multi ： 在事务中被执行的命令数量
     * qbuf ： 查询缓冲区的长度（字节为单位， 0 表示没有分配查询缓冲区）
     * qbuf-free ： 查询缓冲区剩余空间的长度（字节为单位， 0 表示没有剩余空间）
     * obl ： 输出缓冲区的长度（字节为单位， 0 表示没有分配输出缓冲区）
     * oll ： 输出列表包含的对象数量（当输出缓冲区没有剩余空间时，命令回复会以字符串对象的形式被入队到这个队列里）
     * omem ： 输出缓冲区和输出列表占用的内存总量
     * events ： 文件描述符事件
     * cmd ： 最近一次执行的命令
     *
     * 客户端 flag 可以由以下部分组成：
     *
     * O ： 客户端是 MONITOR 模式下的附属节点（slave）
     * S ： 客户端是一般模式下（normal）的附属节点
     * M ： 客户端是主节点（master）
     * x ： 客户端正在执行事务
     * b ： 客户端正在等待阻塞事件
     * i ： 客户端正在等待 VM I/O 操作（已废弃）
     * d ： 一个受监视（watched）的键已被修改， EXEC 命令将失败
     * c : 在将回复完整地写出之后，关闭链接
     * u : 客户端未被阻塞（unblocked）
     * A : 尽可能快地关闭连接
     * N : 未设置任何 flag
     *
     * 文件描述符事件可以是：
     *
     * r : 客户端套接字（在事件 loop 中）是可读的（readable）
     * w : 客户端套接字（在事件 loop 中）是可写的（writeable）
     */
    public function clientList(): array
    {
        return $this->handle->client('list');
    }
}
