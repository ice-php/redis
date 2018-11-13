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
     * @var string
     */
    protected $key;

    /**
     * @var int
     */
    protected $timeout = 30;

    /**
     * 构造一个字符串类
     * @param \redis $redis 主redis句柄
     * @param $key string 键
     */
    public function __construct(\redis $redis, string $key)
    {
        $this->handle = $redis;
        $this->key = $key;

        $this->timeout = Redis::getTimeout();
    }

    /**
     * 删除当前存储对象
     * @return int 删除掉的对象数目
     */
    public function delete():int
    {
        return $this->handle->del($this->key);
    }

    /**
     * 用于序列化给定 key ，并返回被序列化的值
     * 本机测试,总是返回False,???
     * @return string
     */
    public function dump(): string
    {
        return $this->handle->dump($this->key);
    }

    /**
     * 获取当前对象的剩余生存期(秒)
     * @param $milliseconds bool 以毫秒计量
     * @return int
     */
    public function getExpire(bool $milliseconds = false): int
    {
        //以毫秒为单位返回
        if ($milliseconds) {
            return $this->handle->pttl($this->key);
        }

        //以秒为单位返回
        return $this->handle->ttl($this->key);
    }

    /**
     * 将当前存储对象,移到指定的数据库中
     * @param $db string 库名
     * @return bool
     */
    public function move(string $db): bool
    {
        return $this->handle->move($this->key, $db);
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
        $ret = $replace ? $this->handle->rename($this->key, $newName) : $this->handle->renameNx($this->key, $newName);

        //如果改名成功,修改本对象的键
        if ($ret) {
            $this->key = $newName;
        }
        return $ret;
    }

    //返回当前存储对象的类型,字符串格式
    abstract public function getType():string ;

    /**
     * 设置生存时间
     * @param $seconds int 秒
     * @param $milliseconds bool 以毫秒计量
     * @return bool
     */
    public function setExpire(int $seconds, bool $milliseconds = false): bool
    {
        //以毫秒为计量单位
        if ($milliseconds) {
            return $this->handle->pExpire($this->key, $seconds);
        }

        //以秒为单位
        return $this->handle->expire($this->key, intval($seconds));
    }

    /**
     * 设置生成时间(时间戳)
     * @param $timestamp int 时间戳
     * @param $milliseconds bool 以毫秒计量
     * @return bool
     */
    public function setExpireAt(int $timestamp, bool $milliseconds = false): bool
    {
        //以毫秒为计量单位
        if ($milliseconds) {
            return $this->handle->pExpireAt($this->key, $timestamp);
        }

        //以秒为单位
        return $this->handle->expireAt($this->key, intval($timestamp));
    }

    /**
     * 返回引用次数
     * @return int
     */
    public function getRefCount(): int
    {
        return intval($this->handle->object('REFCOUNT', $this->key));
    }

    /**
     * 返回当前存储对象的编码
     * 字符串raw int 列表:ziplist linkedlist 集合:intset hashtable 哈希表:zipmap hashtable 有序集合:ziplist skiplist
     * @return string
     */
    public function getEncoding(): string
    {
        return $this->handle->object('ENCODING', $this->key);
    }

    /**
     * 查看空转时间(无读写)
     * @return int 秒
     */
    public function getIdleTime(): int
    {
        return intval($this->handle->object('IDLETIME', $this->key));
    }

    /**
     * 移除 key 的过期时间，key 将持久保持。
     * @return bool
     */
    public function persist(): bool
    {
        return $this->handle->persist($this->key);
    }

    /**
     * 返回当前存储对象所关联的字符串值
     * @return string
     */
    protected function getRaw():string
    {
        return strval($this->handle->get($this->key));
    }

}