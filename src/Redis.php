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
     * @var RedisConnection 连接对象
     */
    private static $connection;

    /**
     * 获取redis实际句柄
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
    public static function connection(): RedisConnection
    {
        //本句以防止尚未连接
        self::handle();

        //返回连接对象
        return self::$connection;
    }

    /**
     * 删除一个或多个存储对象
     * @param $names string|array 一个或多个存储对象的名称
     * @return int 删除掉多少个存储对象
     */
    public static function delete($names): int
    {
        if (!is_array($names)) {
            $names = [$names];
        }
        return self::handle()->delete(...$names);
    }

    /**
     * 获取一个名称对应的Redis对象
     * @param $name string 存储对象名称
     * @return null|RedisHash|RedisList|RedisSet|RedisSortedSet|RedisString
     */
    public static function get(string $name)
    {
        //获取连接句柄
        $handle = self::handle();

        //获取存储对象的类型
        $type = $handle->type($name);

        switch ($type) {
            case \redis::REDIS_STRING:
                return new RedisString($handle, $name);
            case \redis::REDIS_HASH:
                return new RedisHash($handle, $name);
            case \redis::REDIS_LIST:
                return new RedisList($handle, $name);
            case \redis::REDIS_SET:
                return new RedisSet($handle, $name);
            case \redis::REDIS_ZSET:
                return new RedisSortedSet($handle, $name);
            case \redis::REDIS_NOT_FOUND:
                return null;
            default:
                trigger_error('不识别的Redis数据类型:' . $type, E_USER_ERROR);
                return null;
        }
    }

    /**
     * 获取一个String类型的存储值
     * @param $name String 存储对象的名称
     * @return string
     */
    public static function getString($name): string
    {
        $obj = new RedisString(self::handle(), $name);
        return $obj->get();
    }

    /**
     * 获取一个Hash类型的指定域存储值
     * @param $name String Hash表的名称
     * @param $key string 要读取的Hash键
     * @return string
     */
    public static function getHash(string $name, string $key): string
    {
        $obj = new RedisHash(self::handle(), $name);
        return $obj->get($key);
    }

    /**
     * 获取一个List类型的指定索引存储值
     * @param $name String 列表的名称
     * @param $index int 要读取的列表索引
     * @return string
     */
    public static function getList(string $name, int $index): string
    {
        $obj = new RedisList(self::handle(), $name);
        return $obj->get($index);
    }

    /**
     * 获取一个集合类型的存储值
     * @param $name String 集合的名称
     * @return array 集合的全部值
     */
    public static function getSet(string $name): array
    {
        $obj = new RedisSet(self::handle(), $name);
        return $obj->all();
    }

    /**
     * 获取一个SortedSet类型的存储值
     * @param $name String 有序集合的名称
     * @return array 集合的全部值
     */
    public static function getSortedSet(string $name): array
    {
        $obj = new RedisSortedSet(self::handle(), $name);
        return $obj->all();
    }

    /**
     * 获取所有对象的名称
     * @param $pattern string 模式字符串
     * @return array
     */
    public static function listNames(string $pattern): array
    {
        //获取连接句柄
        return self::handle()->keys($pattern);
    }

    /**
     * 随机获取一个存储对象名称
     * @return string
     */
    public static function getRandomName(): string
    {
        return self::handle()->randomKey();
    }

    /**
     * 判断是否存在指定的存储对象
     * @param $name string 存储对象的名称
     * @return bool
     */
    public static function exists(string $name): bool
    {
        return self::handle()->exists($name);
    }

    /**
     * 返回指定名称的存储类型(原生)
     * @param $name string 存储对象的名称
     * @return int 常量
     */
    public static function getType(string $name): int
    {
        return self::handle()->type($name);
    }

    /**
     * 创建一个String存储对象
     * @param $name string 存储对象的名称
     * @param $value string 值  可选,非空时将覆盖
     * @param int $expire 生存时间(秒)
     * @return RedisString
     */
    public static function createString(string $name, ?string $value = null, int $expire = 0): RedisString
    {
        //创建String对象
        $string = new RedisString(self::handle(), $name);

        //设置值
        if (!is_null($value)) {
            $string->set($value, $expire);
        }

        //返回
        return $string;
    }

    /**
     * 创建一个存储混编类型的存储对象
     * @param string $name 存储对象的名称
     * @param null $value 要存储的值
     * @param int $expire
     * @return RedisJson
     */
    public static function create(string $name, $value = null, int $expire = 0): RedisJson
    {
        $json = new RedisJson(self::handle(), $name);
        if (!is_null($value)) {
            $json->set($value, $expire);
        }
        return $json;
    }

    /**
     * 创建一个BIT存储对象
     * @param $name string Redis存储对象的名称
     * @param int $value 值
     * @param int $expire 生存期
     * @return RedisBit
     */
    public static function createBit(string $name, ?int $value = null, int $expire = 0): RedisBit
    {
        $bit = new RedisBit(self::handle(), $name);
        if (!is_null($value)) {
            $bit->set($value, $expire);
        }
        return $bit;
    }

    /**
     * 创建一个Int存储对象
     * @param $name string Redis存储对象的名称
     * @param int $value 值
     * @param int $expire 生存期
     * @return RedisInt
     */
    public static function createInt(string $name, ?int $value = null, int $expire = 0): RedisInt
    {
        $int = new RedisInt(self::handle(), $name);

        if (!is_null($value)) {
            $int->set($value, $expire);
        }
        return $int;
    }

    /**
     * 创建一个FLOAT存储对象
     * @param $name string Redis存储对象的名称
     * @param float $value 值
     * @param int $expire 生存期
     * @return RedisFloat
     */
    public static function createFloat(string $name, ?float $value = null, int $expire = 0): RedisFloat
    {
        $float = new RedisFloat(self::handle(), $name);
        if (!is_null($value)) {
            $float->set($value, $expire);
        }
        return $float;
    }

    /**
     * 创建一个Hash存储对象
     * @param $name string 名称
     * @param array $fields 初始值
     * @param int $expire 过期时间(秒),默认无限
     * @return RedisHash
     */
    public static function createHash(string $name, ?array $fields = null, int $expire = 0): RedisHash
    {
        //创建Hash对象
        $hash = new RedisHash(self::handle(), $name);

        //如果指定了值,则设置值
        if (!is_null($fields)) {
            $hash->inserts($fields);
        }

        //设置过期时间(秒)
        $hash->setExpire($expire);

        //返回 Hash对象
        return $hash;
    }

    /**
     * 创建一个List存储对象
     * @param $name string 列表对象的名称
     * @param array $values 要保存的值
     * @param int $expire 过期时间(秒),默认无限
     * @return RedisList
     */
    public static function createList(string $name, ?array $values = null, int $expire = 0): RedisList
    {
        //创建对象
        $list = new RedisList(self::handle(), $name);

        //如果指定了值,则添加
        if (!is_null($values)) {
            $list->inserts($values);
        }

        //设置过期时间
        $list->setExpire($expire);

        //返回List对象
        return $list;
    }

    /**
     * 创建一个集合(Set)对象
     * @param $name string  集合对象的名称
     * @param array $members 集合成员数组
     * @param int $expire 过期时间(秒),默认无限
     * @return RedisSet
     */
    public static function createSet(string $name, ?array $members = null, int $expire = 0): RedisSet
    {
        //创建集合对象
        $set = new RedisSet(self::handle(), $name);

        //如果指定了元素,则添加元素
        if (!is_null($members)) {
            $set->inserts($members);
        }

        //设置过期时间
        $set->setExpire($expire);

        //返回集合对象
        return $set;
    }

    /**
     * 创建一个有序集合
     * @param $name string 有序集合名称
     * @param array|null $kvs 集合成员(键值数组)
     * @param int $expire 过期时间(秒),默认无限
     * @return RedisSortedSet
     */
    public static function createSortedSet(string $name, ?array $kvs = null, int $expire = 0): RedisSortedSet
    {
        //创建集合对象
        $set = new RedisSortedSet(self::handle(), $name);

        //如果指定了成员,则添加成员
        if (!is_null($kvs)) {
            $set->inserts($kvs);
        }

        //设置过期时间
        $set->setExpire($expire);

        //返回有序集合对象
        return $set;
    }

    /**
     * 同时存储多个存储对象,如果存在,则覆盖
     * 可应用于字符串/整数/浮点/JSON等 标量类型数据
     * @param array $kvs
     * @return bool
     */
    public static function update(array $kvs): bool
    {
        return self::handle()->mset($kvs);
    }

    /**
     * 同时存储多个存储对象(与update相同)
     * 可应用于字符串/整数/浮点/JSON等 标量类型数据
     * @param array $kvs
     * @return bool
     */
    public static function inserts(array $kvs): bool
    {
        return self::update($kvs);
    }

    /**
     * 返回所有(一个或多个)给定存储对象的值
     * 可应用于字符串/整数/浮点/JSON等 标量类型数据
     * @param array $names
     * @return array 值数组,没有键,与参数严格对应,不存在的用null占位
     */
    public static function col(array $names): array
    {
        return self::handle()->mget($names);
    }


    /**
     * 在指定的多个列表的头部循环查看,如果有元素则弹出,如果全为空,则等待
     * @param array $lists 列表(List)对象的名称数组
     * @param int $timeout 等待时间(s),默认无限等待
     * @return array [列表的名称,弹出的元素]
     */
    public static function popLeftWait(array $lists, int $timeout = 0): array
    {
        return self::handle()->blPop($lists, $timeout);
    }

    /**
     * 在指定的多个列表的尾部循环查看,如果有元素则弹出,如果全为空,则等待
     * @param array $keys 列表(List)对象的名称数组
     * @param int $timeout 等待时间(s),默认无限等待
     * @return array [列表的名称,弹出的元素]
     */
    public static function popRightWait(array $keys, int $timeout = 0): array
    {
        return self::handle()->brPop($keys, $timeout);
    }

    /**
     * 所有给定集合的交集。
     * @param array $sets 集合名称
     * @return array 结果集
     */
    public static function inter(array $sets): array
    {
        return self::handle()->sInter(...$sets);
    }

    /**
     * 计算所有给定集合的并集
     * @param array $sets 集合名称
     * @return array 结果集
     */
    public static function union(array $sets): array
    {
        return self::handle()->sUnion(...$sets);
    }

    /**
     * 计算所有给定集合的差集
     * @param array $sets 集合名称
     * @return array 结果集
     */
    public static function diff(array $sets): array
    {
        return self::handle()->sDiff(...$sets);
    }

    /**
     * 创建一个频道(实际上没干什么事)
     * @param $name string 频道名称
     * @return RedisChannel
     */
    public static function createChannel(string $name): RedisChannel
    {
        return new RedisChannel(self::handle(), $name);
    }

    /**
     * 订阅多个频道
     * @param array $channels 频道名称列表
     * @param callable $func 有消息时的回调方法
     */
    public static function subscribe(array $channels, callable $func): void
    {
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
    public static function selectDb(int $db): bool
    {
        return self::handle()->select($db);
    }

    /**
     * 返回当前数据库的 存储对象 的数量。
     * @return int
     */
    public static function count(): int
    {
        return self::handle()->dbSize();
    }

    /**
     * 对全部存储对象,进行遍历
     * @param string $pattern 匹配(*,?,[])
     * @param int $count 建议每次搜索的数量,应该与键名平均长度成反比
     * @return \Iterator
     */
    public function selectNames(string $pattern = '', int $count = 1000): \Iterator
    {
        $iterator = null;
        while (true) {
            $ret = self::handle()->scan($iterator, $pattern, $count);
            if (false == $ret) {
                break;
            }
            foreach ($ret as $item) {
                yield $item;
            }
        }
    }

    /**
     * 获取指定HLL 并集的近似基数(即集合中唯一元素的个数)
     * @param array $hypers 每个HLL的名称
     * @return int
     */
    public function hyperCount(array $hypers): int
    {
        return self::handle()->pfCount($hypers);
    }
}