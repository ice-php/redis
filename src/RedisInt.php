<?php
declare(strict_types=1);

namespace icePHP;

/**
 * 位类型,String类型的子类型
 */
final class RedisInt extends RedisElement
{
    /**
     * 获取当前存储对象的类型(字符串格式)
     * @return string
     */
    public function getType(): string
    {
        return 'Int';
    }

    /**
     * 数值增减
     * @param $diff int|string 1/-1/N/-N
     * @return int 操作过后的值
     */
    public function increase(int $diff = 1): int
    {
        //加一操作
        if ($diff == 1) {
            return $this->handle->incr($this->name);
        }

        //减一操作
        if ($diff == -1) {
            return $this->handle->decr($this->name);
        }

        //增加
        if ($diff > 0) {
            return $this->handle->incrBy($this->name, $diff);
        }

        //减少
        if ($diff < 0) {
            return $this->handle->decrBy($this->name, abs($diff));
        }

        //如果0,返回原值
        return $this->get();
    }

    /**
     * 整数减量
     * @param int $diff 减量
     * @return int 更新后的值
     */
    public function decrease(int $diff = 1): int
    {
        return $this->increase(-$diff);
    }

    /**
     * 获取当前值,转换成整数
     * @return int
     */
    public function get(): int
    {
        return intval(parent::getRaw());
    }

    /**
     * 设置一个值
     * @param $value int 值
     * @param int $expire 生存期
     * @return bool 成功否
     */
    public function set(int $value, int $expire = 0): bool
    {
        return parent::setString(strval($value), $expire);
    }

    /**
     * 将当前对象的值设为value，并返回旧值。
     * @param $value int 新值
     * @return int 原值
     */
    public function getAndSet(int $value): int
    {
        return intval($this->handle->getSet($this->name, strval($value)));
    }
}