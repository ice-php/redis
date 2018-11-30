<?php
declare(strict_types=1);

namespace icePHP;

/**
 * 所有Redis存储对象的基类
 * Class SRedisElement
 */
abstract class RedisElement
{
    /**
     * @var \redis
     */
    protected $handle;

    /**
     * 当前对象的名称(Redis存储的键)
     * @var string
     */
    protected $name;

    /**
     * 构造一个当前类型的存储对象类
     * @param \redis $redis 主redis句柄
     * @param $name string 名称
     */
    public function __construct(\redis $redis, string $name)
    {
        $this->handle = $redis;
        $this->name = $name;
    }

    /**
     * 删除当前存储对象
     * @return int 删除掉的对象数目
     */
    public function deleteSelf(): int
    {
        return $this->handle->del($this->name);
    }

    /**
     * 返回当前对象的存储值的内部存储结构，使用 RESTORE 命令可以将这个值反序列化为 Redis 键。
     * @return string
     */
    public function dump(): string
    {
        return $this->handle->dump($this->name);
    }

    /**
     * 将DUMP出的结果,存储回Redis服务器,以当前对象名称
     * @param string $data DUMP出来的数据
     * @param int $timeout 过期时间(秒),默认无限制
     * @return bool
     */
    public function restore(string $data, int $timeout = 0): bool
    {
        return $this->handle->restore($this->name, $timeout, $data);
    }

    /**
     * 判断当前对象是否已经存储在Redis服务器上
     * @return bool
     */
    public function existsSelf(): bool
    {
        return $this->handle->exists($this->name);
    }

    /**
     * 设置过期时间(秒)
     * @param int $seconds
     * @return bool 成功与否
     */
    public function setExpire(int $seconds): bool
    {
        return $this->handle->expire($this->name, $seconds);
    }

    /**
     * 设置过期时间(毫秒)
     * @param int $ms
     * @return bool 成功与否
     */
    public function setExpireMilliSeconds(int $ms): bool
    {
        return $this->handle->pExpire($this->name, $ms);
    }

    /**
     * 设置过期时间点(秒,时间戳)
     * @param int $timestamp 时间戳
     * @return bool 成功与否
     */
    public function setExpireAt(int $timestamp): bool
    {
        return $this->handle->expireAt($this->name, $timestamp);
    }

    /**
     * 设置过期时间点(毫秒,时间戳)
     * @param int $timestamp 时间戳
     * @return bool 成功与否
     */
    public function setExpireAtMilliSeconds(int $timestamp): bool
    {
        return $this->handle->pExpireAt($this->name, $timestamp);
    }

    /**
     * 复制到另一个服务器上
     * ? 如果目标服务器要求密码怎么办?
     * @param string $host 主机
     * @param int $port 端口,默认6379
     * @param int $db 数据库编号,默认0
     * @param int|null $timeout 超时限制,默认无限
     * @param bool $replace 是否覆盖同名目标
     * @return bool 成功与否
     */
    public function moveToServer(string $host, $port = 6379, int $db = 0, int $timeout = 0, bool $replace = true): bool
    {
        return $this->handle->migrate($host, $port, $this->name, $db, $timeout, false, $replace);
    }

    /**
     * 迁移到另一个服务器上
     * ? 如果目标服务器要求密码怎么办?
     * @param string $host 主机
     * @param int $port 端口,默认6379
     * @param int $db 数据库编号,默认0
     * @param int|null $timeout 超时限制,默认无限
     * @param bool $replace 是否覆盖同名目标
     * @return bool 成功与否
     */
    public function copyToServer(string $host, $port, int $db, int $timeout = 0, bool $replace = true):bool
    {
        return $this->handle->migrate($host, $port, $this->name, $db, $timeout, true, $replace);
    }

    /**
     * 将当前存储对象,移到指定的数据库中
     * @param $db int 库名(整数,0-15)
     * @return bool 本身不存在或有同名目标,则失败
     */
    public function moveToDb(int $db): bool
    {
        return $this->handle->move($this->name, $db);
    }

    /**
     * 返回引用次数
     * 返回当前对象 引用所储存的值的次数。此命令主要用于除错。
     * 本机测试,通常是1
     * @return int
     */
    public function getRefCount(): int
    {
        return intval($this->handle->object('REFCOUNT', $this->name));
    }

    /**
     * 返回当前存储对象的编码
     * 返回 当前对象 锁储存的值所使用的内部表示(representation)。
     * 字符串可以被编码为 raw (一般字符串)或 int (为了节约内存，Redis 会将字符串表示的 64 位有符号整数编码为整数来进行储存）。
     * 列表可以被编码为 ziplist 或 linkedlist 。 ziplist 是为节约大小较小的列表空间而作的特殊表示。
     * 集合可以被编码为 intset 或者 hashtable 。 intset 是只储存数字的小集合的特殊表示。
     * 哈希表可以编码为 zipmap 或者 hashtable 。 zipmap 是小哈希表的特殊表示。
     * 有序集合可以被编码为 ziplist 或者 skiplist 格式。 ziplist 用于表示小的有序集合，而 skiplist 则用于表示任何大小的有序集合。
     * @return string
     */
    public function getEncoding(): string
    {
        return $this->handle->object('ENCODING', $this->name);
    }

    /**
     * 查看空转时间(无读写)
     * 返回当前对象 自储存以来的空闲时间(idle， 没有被读取也没有被写入)，以秒为单位。
     * @return int 秒
     */
    public function getIdleTime(): int
    {
        return intval($this->handle->object('IDLETIME', $this->name));
    }

    /**
     * 移除 key 的过期时间，key 将持久保持。
     * @return bool
     */
    public function persist(): bool
    {
        return $this->handle->persist($this->name);
    }

    /**
     * 获取当前对象的过期时间(秒)
     * @return int  -1表示永久,-2表示对象不存在
     */
    public function getExpire(): int
    {
        return $this->handle->ttl($this->name);
    }

    /**
     * 获取当前对象的过期时间(毫秒)
     * @return int  -1表示永久,-2表示对象不存在
     */
    public function getExpireMilliSeconds(): int
    {
        return $this->handle->pttl($this->name);
    }

    /**
     * 修改当前存储对象的键名
     * @param $newName string 新的键名
     * @param bool $replace 如果新的键名存在,是否覆盖
     * @return bool 修改是否成功
     */
    public function rename(string $newName, bool $replace = true): bool
    {
        //根据是否覆盖
        $ret = $replace ? $this->handle->rename($this->name, $newName) : $this->handle->renameNx($this->name, $newName);

        //如果改名成功,修改本对象的键
        if ($ret) {
            $this->name = $newName;
        }
        return $ret;
    }


    /**
     * 返回当前存储对象的类型,REDIS常量
     * @return int
     * - string: Redis::REDIS_STRING
     * - set:   Redis::REDIS_SET
     * - list:  Redis::REDIS_LIST
     * - zset:  Redis::REDIS_ZSET
     * - hash:  Redis::REDIS_HASH
     * - other: Redis::REDIS_NOT_FOUND
     */
    public function getTypeConst(): int
    {
        return $this->handle->type($this->name);
    }

    /**
     * 返回当前存储对象的类型,字符串格式
     * 这个不如getTypeConst准确
     * @return string
     */
    abstract public function getType(): string;

    /**
     * 返回当前存储对象所关联的字符串值
     * @return string
     */
    protected function getRaw(): string
    {
        return strval($this->handle->get($this->name));
    }

    /**
     * 设置一个键值
     * @param $value string 值
     * @param int $expire 生存期
     * @return bool 成功否
     */
    public function setString(string $value, int $expire = 0): bool
    {
        $handle = $this->handle;

        //覆盖并设置生存时间
        if ($expire) {
            return $handle->setex($this->name, $expire, $value);
        }

        //仅覆盖
        return $handle->set($this->name, $value);
    }
}