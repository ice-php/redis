<?php
declare(strict_types=1);

namespace icePHP;

/**
 * Created by PhpStorm.
 * User: zhangQiang
 * Date: 2017/7/18
 * Time: 11:19
 */
final class Redis
{
    /**
     * 禁止实例化,本类为静态类
     */
    private function __construct()
    {
    }

    /**
     * @var \redis redis实际句柄
     */
    private static $handle;

    /**
     * @var RedisConnection
     */
    private static $connection;

    /**
     * 获取连接对象
     */
    private static function handle(): \redis
    {
        //如果已经连接,返回连接句柄
        if (self::$handle) {
            return self::$handle;
        }

        //创建连接对象实例
        self::$connection = RedisConnection::instance();

        //生成实际Redis句柄
        self::$handle = self::$connection->connect();

        //返回连接句柄
        return self::$handle;
    }

    /**
     * 获取连接对象
     * @return RedisConnection
     */
    public static function connection():RedisConnection
    {
        //本句以防止尚未连接
        self::handle();

        //返回连接对象
        return self::$connection;
    }

    /**
     * 删除一个存储键或多个
     * @param $key string|array 一个或多个键
     * @return int
     */
    public static function delete($key):int
    {
        return self::handle()->delete($key);
    }

    /**
     * 获取一个键对应的Redis对象
     * @param $key string 键
     * @return null|RedisHash|RedisList|RedisSet|RedisSortedSet|RedisString
     */
    public static function get(string $key)
    {
        //获取连接句柄
        $handle = self::handle();

        //获取存储对象的类型
        $type = $handle->type($key);

        switch ($type) {
            case \redis::REDIS_STRING:
                return new RedisString($handle, $key);
            case \redis::REDIS_HASH:
                return new RedisHash($handle, $key);
            case \redis::REDIS_LIST:
                return new RedisList($handle, $key);
            case \redis::REDIS_SET:
                return new RedisSet($handle, $key);
            case \redis::REDIS_ZSET:
                return new RedisSortedSet($handle, $key);
            case \redis::REDIS_NOT_FOUND:
                return null;
            default:
                trigger_error('不识别的Redis数据类型:' . $type, E_USER_ERROR);
                return null;
        }
    }

    /**
     * 获取一个String类型的存储值
     * @param $key String 键
     * @return string
     */
    public static function getString($key): string
    {
        $obj = new RedisString(self::handle(), $key);
        return $obj->get();
    }

    /**
     * 获取一个Hash类型的指定域存储值
     * @param $key String 键
     * @param $field string 域
     * @return string
     */
    public static function getHash(string $key, string $field): string
    {
        $obj = new RedisHash(self::handle(), $key);
        return $obj->get($field);
    }

    /**
     * 获取一个List类型的指定索引存储值
     * @param $key String 键
     * @param $index string 索引
     * @return string
     */
    public static function getList(string $key,string $index): string
    {
        $obj = new RedisList(self::handle(), $key);
        return $obj->get($index);
    }

    /**
     * 获取一个集合类型的存储值
     * @param $key String 键
     * @return array
     */
    public static function getSet(string $key): array
    {
        $obj = new RedisSet(self::handle(), $key);
        return $obj->get();
    }

    /**
     * 获取一个SortedSet类型的存储值
     * @param $key String 键
     * @return array
     */
    public static function getSortedSet(string $key): array
    {
        $obj = new RedisSortedSet(self::handle(), $key);
        return $obj->get();
    }

    /**
     * 获取所有键
     * @param $pattern string 模式字符串
     * @return array
     */
    public static function listKeys(string $pattern): array
    {
        //获取连接句柄
        return self::handle()->keys($pattern);
    }

    /**
     * 随机获取一个键
     * @return string
     */
    public static function getRandomKey(): string
    {
        return self::handle()->randomKey();
    }

    /**
     * 判断是否存在指定的键
     * @param $key string 键
     * @return bool
     */
    public static function exists(string $key): bool
    {
        return self::handle()->exists($key);
    }

    /**
     * 返回指定键的存储类型(原生)
     * @param $key string 键
     * @return int 常量
     */
    public static function getType(string $key): int
    {
        return self::handle()->type($key);
    }

    /**
     * 创建一个String存储对象
     * @param $key string 键
     * @param $value string 值
     * @param bool $replace 存在时是否覆盖
     * @param int $expire 生存时间(秒)
     * @return RedisString
     */
    public static function createString(string $key, string $value = '',bool $replace = true, int $expire = 0): RedisString
    {
        //创建String对象
        $string = new RedisString(self::handle(), $key);

        //设置值
        $string->set($value, $replace, $expire);

        //返回
        return $string;
    }

    /**
     * 创建一个BIT存储对象
     * @param $key string Redis 键
     * @param int $value 值
     * @param bool $replace 是否覆盖
     * @param int $expire 生存期
     * @return RedisBit
     */
    public static function createBit(string $key,int $value = 0, bool $replace = true,int $expire = 0): RedisBit
    {
        $bit = new RedisBit(self::handle(), $key);
        $bit->set($value, $replace, $expire);
        return $bit;
    }

    /**
     * 创建一个Int存储对象
     * @param $key string Redis 键
     * @param int $value 值
     * @param bool $replace 是否覆盖
     * @param int $expire 生存期
     * @return RedisInt
     */
    public static function createInt(string $key,int $value = 0,bool $replace = true, int $expire = 0): RedisInt
    {
        $int = new RedisInt(self::handle(), $key);
        $int->set($value, $replace, $expire);
        return $int;
    }

    /**
     * 创建一个FLOAT存储对象
     * @param $key string Redis 键
     * @param float $value 值
     * @param bool $replace 是否覆盖
     * @param int $expire 生存期
     * @return RedisFloat
     */
    public static function createFloat(string $key,float $value = 0.0,bool $replace = true, int $expire = 0): RedisFloat
    {
        $float = new RedisFloat(self::handle(), $key);
        $float->set($value, $replace, $expire);
        return $float;
    }

    /**
     * 创建一个Hash存储对象
     * @param $key string 名称
     * @param array $fields 初始值
     * @return RedisHash
     */
    public static function createHash(string $key, array $fields = null): RedisHash
    {
        //创建Hash对象
        $hash = new RedisHash(self::handle(), $key);

        //如果指定了值,则设置值
        if ($fields) {
            $hash->multiSet($fields);
        }

        //返回 Hash对象
        return $hash;
    }

    /**
     * 创建一个List存储对象
     * @param $key string 键名
     * @param mixed $values 要保存的值
     * @return RedisList
     */
    public static function createList(string $key, $values = null): RedisList
    {
        //创建对象
        $list = new RedisList(self::handle(), $key);

        //如果指定了值,则添加
        if ($values) {
            $list->insert($values);
        }

        //返回List对象
        return $list;
    }

    /**
     * 创建一个集合(Set)对象
     * @param $key string  键
     * @param mixed $members 元素或元素数组
     * @return RedisSet
     */
    public static function createSet(string $key, $members = null): RedisSet
    {
        //创建集合对象
        $set = new RedisSet(self::handle(), $key);

        //如果指定了元素,则添加元素
        if (is_array($members)) {
            $set->addMulti($members);
        } elseif ($members) {
            $set->add($members);
        }

        //返回集合对象
        return $set;
    }

    /**
     * 创建一个有序集合
     * @param $key string 键
     * @param array|null $members 成员
     * @return RedisSortedSet
     */
    public static function createSortedSet(string $key, array $members = null): RedisSortedSet
    {
        $set = new RedisSortedSet(self::handle(), $key);
        if ($members) {
            $set->addMulti($members);
        }
        return $set;
    }

    /**
     * 同时存储多个键值对,覆盖
     * @param array $kvs 键值对
     * @param bool $replace 是否覆盖
     * @return bool|int
     */
    public static function multiSet(array $kvs, bool $replace = true)
    {
        if ($replace) {
            return self::handle()->mset($kvs);
        }
        return self::handle()->msetnx($kvs);
    }

    /**
     * 返回所有(一个或多个)给定key的值。
     * @param array $keys
     * @return array
     */
    public static function multiGet(array $keys): array
    {
        return self::handle()->mget($keys);
    }

    /**
     * 取配置中的超时设置
     * @return int
     */
    public static function getTimeout():int
    {
        return intval(configDefault(30, 'redis', 'timeout'));
    }

    /**
     * 阻塞式头部弹出
     * @param array $keys 列表(List)的键名数组
     * @return array
     */
    public static function blockLeftPop(array $keys): array
    {
        return self::handle()->blPop($keys, self::getTimeout());
    }

    /**
     * 阻塞式尾部弹出
     * @param array $keys 列表(List)的键名数组
     * @return array
     */
    public static function blockRightPop(array $keys): array
    {
        return self::handle()->brPop($keys, self::getTimeout());
    }

    /**
     * 所有给定集合的交集。
     * @return array 结果集
     */
    public static function inter(): array
    {
        $sets = func_get_args();
        return call_user_func_array([self::handle(), 'sInter'], $sets);
    }

    /**
     * 计算所有给定集合的并集
     * @return array 结果集
     */
    public static function union(): array
    {
        $sets = func_get_args();
        return call_user_func_array([self::handle(), 'sUnion'], $sets);
    }

    /**
     * 计算所有给定集合的差集
     * @return array 结果集
     */
    public static function diff(): array
    {
        $sets = func_get_args();
        return call_user_func_array([self::handle(), 'sDiff'], $sets);
    }

    /**
     * 创建一个频道(实际上没干什么事)
     * @param $key string 频道名称
     * @return RedisChannel
     */
    public static function createChannel(string $key): RedisChannel
    {
        return new RedisChannel(self::handle(), $key);
    }

    /**
     * 订阅多个频道
     * @param callable $func 有消息时的回调方法
     */
    public static function subscribe(callable $func):void
    {
        $channels = func_get_args();
        self::handle()->subscribe($channels, $func);
    }

    /**
     * 订阅频道,按模式匹配
     * @param array $patterns 模式 (可以使用*)
     * @param callable $func
     */
    public static function subscribeByPattern(array $patterns, callable $func): void
    {
        self::handle()->psubscribe($patterns, $func);
    }

    /**
     * 返回事务对象(单例)
     * @return RedisTransaction
     */
    public static function transaction(): RedisTransaction
    {
        static $object;
        if (!$object) {
            $object = new RedisTransaction(self::handle());
        }
        return $object;
    }

    /**
     * 返回服务器对象(单例)
     * @return RedisServer
     */
    public static function server(): RedisServer
    {
        static $object;
        if (!$object) {
            $object = new RedisServer(self::handle());
        }
        return $object;
    }

    /**
     * 切换到指定的数据库，数据库索引号用数字值指定，以 0 作为起始索引值。
     * @param $db int 数据库索引号
     * @return bool
     */
    public static function selectDb($db): bool
    {
        return self::handle()->select($db);
    }

    /**
     * 返回当前数据库的 key 的数量。
     * @return int
     */
    public static function length(): int
    {
        return self::handle()->dbSize();
    }

    /**
     * 从当前游标开始访问指定数量的键
     * @param int $iterator 游标(最初以0开始)
     * @param string $pattern 匹配
     * @param int $count 返回数量
     * @return array|bool 返回的新游标和元素,如果新的游标为0,表示结束
     */
    public function scan(int $iterator = 0,string $pattern = '',int $count = 0)
    {
        return self::handle()->scan($iterator, $pattern, $count);
    }
}