<?php
declare(strict_types=1);

namespace icePHP;

/**
 * 位类型,String类型的子类型
 */
final class RedisInt extends RedisString
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
     * @param $diff int 1/-1/N/-N
     * @return int 操作过后的值
     * @throws \Exception
     */
    public function crease($diff = 1): int
    {
        //参数修正为整数
        $diff = intval($diff);

        //增加
        if ($diff > 0) {
            return $this->handle->incrBy($this->key, $diff);
        }

        //减少
        if ($diff < 0) {
            return $this->handle->decrBy($this->key, abs($diff));
        }

        //不能为0
        throw new \Exception('Crease Method parameter wrong:' . $diff);
    }


    /**
     * 获取当前缓存值,转换成整数
     * @return int
     */
    public function get(): int
    {
        return intval(parent::get());
    }
}