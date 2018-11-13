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
     * 将哈希表key中的域field的值设为value。
     * @param $field string 域名
     * @param $value mixed 值
     * @param bool $replace 是否覆盖
     * @return bool|int
     */
    public function set(string $field, $value, bool $replace = true)
    {
        //覆盖操作
        if ($replace) {
            return $this->handle->hSet($this->key, $field, $value);
        }
        return $this->handle->hSetNx($this->key, $field, $value);
    }

    /**
     * 返回哈希表key中给定域field的值。
     * @param $field string 域名
     * @return string
     */
    public function get(string $field): string
    {
        return $this->handle->hGet($this->key, $field);
    }

    /**
     * 返回哈希表key中，一个或多个给定域的值。
     * @param $fields array 域名列表
     * @return array
     */
    public function multiGet(array $fields): array
    {
        return $this->handle->hMGet($this->key, $fields);
    }

    /**
     * 同时将多个field - value(域-值)对设置到哈希表key中。此命令会覆盖哈希表中已存在的域。
     * @param array $kvs
     * @return bool
     */
    public function multiSet(array $kvs): bool
    {
        return $this->handle->hMset($this->key, $kvs);
    }

    /**
     * 返回哈希表key中，所有的域和值。
     * @return array
     */
    public function listAll(): array
    {
        return $this->handle->hGetAll($this->key);
    }

    /**
     * 删除哈希表key中的一个或多个指定域，不存在的域将被忽略。
     * @return mixed
     */
    public function deleteField()
    {
        $argv = func_get_args();
        array_unshift($argv, $this->key);

        return call_user_func_array([$this->handle, 'hDel'], $argv);
    }

    /**
     * 返回哈希表key中域的数量。
     * @return int
     */
    public function length(): int
    {
        return $this->handle->hLen($this->key);
    }

    /**
     * 查看哈希表key中，给定域field是否存在。
     * @param $field string 域名
     * @return bool
     */
    public function exists($field): bool
    {
        return $this->handle->hExists($this->key, $field);
    }

    /**
     * 为哈希表key中的域field的值加上增量increment。增量也可以为负数，相当于对给定域进行减法操作。
     * @param $field string 域名
     * @param $diff int|float
     * @return float|int
     */
    public function crease(string $field, $diff = 1)
    {
        if (is_int($diff)) {
            return $this->handle->hIncrBy($this->key, $field, $diff);
        }
        return $this->handle->hIncrByFloat($this->key, $field, $diff);
    }

    /**
     * 返回哈希表key中的所有域。
     * @return array
     */
    public function listKeys(): array
    {
        return $this->handle->hKeys($this->key);
    }

    /**
     * 返回哈希表key中的所有值。
     * @return array
     */
    public function listValues(): array
    {
        return $this->handle->hVals($this->key);
    }

    /**
     * 从当前游标开始访问指定数量的元素
     * @param int $iterator 游标(最初以0开始)
     * @param string $pattern 匹配
     * @param int $count 返回数量
     * @return array|bool 返回的新游标和元素,如果新的游标为0,表示结束
     */
    public function scan(int $iterator = 0,string $pattern = '', int $count = 0): array
    {
        return $this->handle->hScan($this->key, $iterator, $pattern, $count);
    }
}
