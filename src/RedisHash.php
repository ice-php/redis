<?php
declare(strict_types=1);

namespace icePHP;

/**
 * 哈希表(Hash)类型
 */
final class RedisHash extends RedisElement
{
    /**
     * 获取当前存储对象的类型(字符串格式)
     * @return string
     */
    public function getType(): string
    {
        return 'Hash';
    }

    /**
     * 将哈希表中的指定键的值设为value。
     * @param $key string 键名
     * @param $value mixed 值
     * @return int
     */
    public function insert(string $key, $value): int
    {
        return $this->handle->hSet($this->name, $key, $value) ?: 0;
    }

    /**
     * 同时将多个key - value(键-值)对设置到哈希表中。此命令会覆盖哈希表中已存在的键。
     * @param array $kvs
     * @return bool
     */
    public function inserts(array $kvs): bool
    {
        return $this->handle->hMset($this->name, $kvs);
    }

    /**
     * 删除哈希表key中的一个或多个指定键，不存在的域将被忽略。
     * @param $keys string|array
     * @return int
     */
    public function delete($keys): int
    {
        if (!is_array($keys)) {
            $keys = [$keys];
        }
        return $this->handle->hDel($this->name, ...$keys) ?: 0;
    }

    /**
     * 查看哈希表中，给定键是否存在。
     * @param $key string 键名
     * @return bool
     */
    public function exists(string $key): bool
    {
        return $this->handle->hExists($this->name, $key);
    }

    /**
     * 返回哈希表中给定键的值。
     * @param $key string 域名
     * @return string
     */
    public function get(string $key): string
    {
        return $this->handle->hGet($this->name, $key);
    }

    /**
     * 返回哈希表中，一个或多个给定键的值。
     * @param $keys array 域名列表
     * @return array
     */
    public function col(array $keys): array
    {
        return $this->handle->hMGet($this->name, $keys) ?: [];
    }

    /**
     * 返回哈希表中，所有的键和值。
     * @return array
     */
    public function all(): array
    {
        return $this->handle->hGetAll($this->name);
    }

    /**
     * 返回哈希表中所有键的数量。
     * @return int
     */
    public function count(): int
    {
        return $this->handle->hLen($this->name);
    }

    /**
     * 返回哈希表中的所有键。
     * @return array
     */
    public function listKeys(): array
    {
        return $this->handle->hKeys($this->name);
    }

    /**
     * 为哈希表中的键的值加上增量increment。增量也可以为负数，相当于对给定域进行减法操作。
     * @param $key string 键名
     * @param $diff int|float
     * @return float|int 更新之后的值
     */
    public function increase(string $key, $diff = 1)
    {
        if (is_int($diff)) {
            return $this->handle->hIncrBy($this->name, $key, $diff);
        }
        return $this->handle->hIncrByFloat($this->name, $key, $diff);
    }

    /**
     * 对指定键的值,进行减量
     * @param string $key
     * @param int|float $diff
     * @return float|int 更新之后的值
     */
    public function decrease(string $key, $diff = 1)
    {
        return $this->increase($key, -$diff);
    }

    /**
     * 返回哈希表key中的所有值。
     * @return array
     */
    public function listValues(): array
    {
        return $this->handle->hVals($this->name);
    }

    /**
     * 返回哈希表中指定KEY的值的字符串长度
     * @param $key string
     * @return int
     */
    public function strlen(string $key): int
    {
        return $this->handle->rawCommand('hstrlen', [$this->name, $key]);
    }

    /**
     * 从当前游标开始访问指定数量的元素
     * @param string $pattern 匹配 * ? []
     * @param int $count 建议一次搜索的量
     * @return \Iterator
     */
    public function select(string $pattern = '', int $count = 100): \Iterator
    {
        $iterator = null;
        while (true) {
            $ret = $this->handle->hScan($this->name, $iterator, $pattern, $count);
            if (false === $ret) {
                break;
            }
            foreach ($ret as $k => $v) {
                yield [$k => $v];
            }
        }
    }
}
