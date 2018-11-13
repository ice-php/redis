<?php
declare(strict_types=1);

namespace icePHP;

/**
 * 创建一个String存储对象
 * @param $key string 键
 * @param $value string 值
 * @param bool $replace 存在时是否覆盖
 * @param int $expire 生存时间(秒)
 * @return RedisString
 */
function redisString(string $key, string $value = '', bool $replace = true, int $expire = 0): RedisString
{
    return Redis::createString($key, $value, $replace, $expire);
}

/**
 * 创建一个String存储对象,与redisString 相同
 * @param $key string 键
 * @param $value string 值
 * @param bool $replace 存在时是否覆盖
 * @param int $expire 生存时间(秒)
 * @return RedisString
 */
function redis(string $key, string $value = '', bool $replace = true, int $expire = 0): RedisString
{
    return Redis::createString($key, $value, $replace, $expire);
}


/**
 * 创建一个Int存储对象
 * @param $key string Redis 键
 * @param int $value 值
 * @param bool $replace 是否覆盖
 * @param int $expire 生存期
 * @return RedisInt
 */
function redisInt(string $key, int $value = 0, bool $replace = true, int $expire = 0): RedisInt
{
    return Redis::createInt($key, $value, $replace, $expire);
}

/**
 * 创建一个FLOAT存储对象
 * @param $key string Redis 键
 * @param float $value 值
 * @param bool $replace 是否覆盖
 * @param int $expire 生存期
 * @return RedisFloat
 */
function redisFloat(string $key, float $value = 0.0, bool $replace = true, int $expire = 0): RedisFloat
{
    return Redis::createFloat($key, $value, $replace, $expire);
}

/**
 * 创建一个BIT存储对象
 * @param $key string Redis 键
 * @param int $value 值
 * @param bool $replace 是否覆盖
 * @param int $expire 生存期
 * @return RedisBit
 */
function redisBit(string $key, int $value = 0, bool $replace = true, int $expire = 0): RedisBit
{
    return Redis::createBit($key, $value, $replace, $expire);
}

/**
 * 创建一个Hash存储对象
 * @param $key string 名称
 * @param array $fields 初始值
 * @return RedisHash
 */
function redisHash(string $key, array $fields = null): RedisHash
{
    return Redis::createHash($key, $fields);
}

/**
 * 创建一个List存储对象
 * @param $key string 键名
 * @param mixed $values 要保存的值
 * @return RedisList
 */
function redisList(string $key, $values = null): RedisList
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