<?php

use icePHP\RedisBit;

/**
 * 用于概率统计的一种算法
 * User: 蓝冰大侠
 * Date: 2018/11/30
 * Time: 12:28
 */
class HyperLogLog extends RedisBit
{
    /**
     * 获取当前存储对象的类型(字符串格式)
     * @return string
     */
    public function getType(): string
    {
        return 'HyperLogLog';
    }

    /**
     * 向HLL集合中插入一个值
     * @param string $value
     * @return bool
     */
    public function insert(string $value): bool
    {
        return $this->handle->pfAdd($this->name, [$value]);
    }

    /**
     * 向HLL集合中插入多个值
     * @param array $values
     * @return bool
     */
    public function inserts(array $values): bool
    {
        return $this->handle->pfAdd($this->name, $values);
    }

    /**
     * 获取本集合中唯一元素的近似基数(个数)
     * @return int
     */
    public function count(): int
    {
        return $this->handle->pfCount($this->name);
    }

    /**
     * 合并多个HLL集合到当前集合
     * @param array $hypers
     * @return bool
     */
    public function merge(array $hypers): bool
    {
        return $this->handle->pfMerge($this->name, $hypers);
    }
}