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
     * 异步(Asynchronously)重写 AOF 文件以反应当前数据库的状态。
     * @internal
     * @return bool
     */
    public function bgRewriteAOF():bool
    {
        return $this->handle->bgrewriteaof();
    }

    /**
     * 在后台异步保存当前数据库的数据到磁盘。
     * @internal
     * @return bool
     */
    public function bgSave():bool
    {
        return $this->handle->bgsave();
    }

    /**
     * 同步保存当前数据库的数据到磁盘。
     * @internal
     * @return bool
     */
    public function save():bool
    {
        return $this->handle->save();
    }

    /**
     * 返回最近一次 Redis 成功执行保存操作的时间点( SAVE 、 BGSAVE 等)，以 UNIX 时间戳格式表示。
     * @internal
     * @return int
     */
    public function lastSave():int
    {
        return $this->handle->lastSave();
    }

    /**
     * SLAVEOF 命令用于在 Redis 运行时动态地修改复制(replication)功能的行为。
     * @internal
     * @param $host string
     * @param $port string
     * @return bool
     */
    public function slaveEOF($host, $port):bool
    {
        return $this->handle->slaveof($host, $port);
    }

    /**
     * 清空整个 Redis 服务器的数据(删除所有数据库的所有 key)。
     * @internal
     * @return bool
     */
    public function flushAll():bool
    {
        return $this->handle->flushAll();
    }

    /**
     * 清空当前数据库中的所有 key 。
     * @internal
     * @return bool
     */
    public function flushDB():bool
    {
        return $this->handle->flushDB();
    }

    /**
     * 获取一定数量的慢日志
     * @param null $count
     * @return mixed
     */
    public function slowLogGet($count = null)
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
    public function configGet($key = '*'):array
    {
        //这个参数就是这个样子的,只有两个
        return $this->handle->config('GET', $key,null);
    }

    /**
     * 设置配置信息
     * @param $key string  配置项名称
     * @param $value string  配置值
     * @return array
     */
    public function configSet($key, $value):array
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
    public function clientList():array
    {
        return $this->handle->client('list');
    }

    /**
     * 返回 CLIENT SETNAME 命令为连接设置的名字。 因为新创建的连接默认是没有名字的， 对于没有名字的连接， CLIENT GETNAME 返回空白回复。
     * @return mixed
     */
    public function getName()
    {
        return $this->handle->client('getname');
    }

    /**
     * 设置CLIENT的名称
     * @param $name string
     * @return bool return true if it can be set and false if not
     */
    public function setName($name)
    {
        return $this->handle->client('setname', $name);
    }

    /**
     * @param $ip
     * @param $port
     * @return mixed
     */
    public function kill($ip, $port)
    {
        return $this->handle->client('kill', $ip . ':' . $port);
    }
}
