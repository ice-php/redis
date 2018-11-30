<?php
declare(strict_types=1);

namespace icePHP;

/**
 * 创建一个String存储对象
 * @param $key string 键
 * @param $value string 值
 * @param int $expire 生存时间(秒)
 * @return RedisString
 */
function redisString(string $key, ?string $value = null, int $expire = 0): RedisString
{
    return Redis::createString($key, $value, $expire);
}

/**
 * 创建一个Json存储对象,可存储任意类型的值
 * @param $key string 键
 * @param $value mixed 值
 * @param int $expire 生存时间(秒)
 * @return RedisJson
 */
function redis(string $key, $value = null, int $expire = 0): RedisJson
{
    return Redis::create($key, $value, $expire);
}

/**
 * 创建一个Int存储对象
 * @param $key string Redis 键
 * @param int $value 值
 * @param int $expire 生存期
 * @return RedisInt
 */
function redisInt(string $key, ?int $value = null, int $expire = 0): RedisInt
{
    return Redis::createInt($key, $value, $expire);
}

/**
 * 创建一个FLOAT存储对象
 * @param $key string Redis 键
 * @param float $value 值
 * @param int $expire 生存期
 * @return RedisFloat
 */
function redisFloat(string $key, ?float $value = null, int $expire = 0): RedisFloat
{
    return Redis::createFloat($key, $value, $expire);
}

/**
 * 创建一个BIT存储对象
 * @param $key string Redis 键
 * @param int $value 值
 * @param int $expire 生存期
 * @return RedisBit
 */
function redisBit(string $key, ?int $value = null, int $expire = 0): RedisBit
{
    return Redis::createBit($key, $value, $expire);
}

/**
 * 创建一个Hash存储对象
 * @param $key string 名称
 * @param array $fields 初始值
 * @return RedisHash
 */
function redisHash(string $key, ?array $fields = null): RedisHash
{
    return Redis::createHash($key, $fields);
}

/**
 * 创建一个List存储对象
 * @param $key string 键名
 * @param mixed $values 要保存的值
 * @return RedisList
 */
function redisList(string $key, ?array $values = null): RedisList
{
    return Redis::createList($key, $values);
}

/**
 * 创建一个集合(Set)对象
 * @param $key string  键
 * @param mixed $members 元素或元素数组
 * @return RedisSet
 */
function redisSet(string $key, $members = null): RedisSet
{
    return Redis::createSet($key, $members);
}

/**
 * 创建一个有序集合
 * @param $key string 键
 * @param array|null $members 成员
 * @return RedisSortedSet
 */
function redisSortedSet(string $key, array $members = null): RedisSortedSet
{
    return Redis::createSortedSet($key, $members);
}

/**
 * 删除一个存储键或多个
 * @param $keys string|array 一个或多个键
 * @return int 删除掉多少个值
 */
function redisDelete(...$keys): int
{
    return Redis::delete(...$keys);
}

/**
 * 判断是否存在指定的键
 * @param $key string 键
 * @return bool
 */
function redisExists(string $key): bool
{
    return Redis::exists($key);
}

/**
 * 返回一个Redis服务器对象
 * @return RedisServer
 */
function redisServer(): RedisServer
{
    return Redis::server();
}

/**
 * 返回一个Redis脚本对象
 * @return RedisScript
 */
function redisScript(): RedisScript
{
    return Redis::script();
}

/**
 * 返回一个Redis连接对象
 * @return RedisConnection
 */
function redisConnection(): RedisConnection
{
    return Redis::connection();
}

/**
 * 获取一个Redis原始句柄
 * @return \redis
 */
function redisHandle(): \redis
{
    return Redis::handle();
}

