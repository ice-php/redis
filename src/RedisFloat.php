<?php
declare(strict_types=1);

namespace icePHP;

/**
 * 位类型,String类型的子类型
 */
final class RedisFloat extends RedisString
{
    /**
     * 获取当前存储对象的类型(字符串格式)
     * @return string
     */
    public function getType(): string
    {
        return 'Float';
    }

    /**
     * 数值增减(浮点)
     * @param $diff int 1/-1/N/-N
     * @return float 操作过后的值
     */
    public function crease($diff = 1): float
    {
        //参数修正为浮点
        $diff = floatval($diff);

        return $this->handle->incrByFloat($this->key, $diff);
    }

    /**
     * 获取当前缓存值,转换成浮点
     * @return float
     */
    public function get(): float
    {
        return floatval(parent::get());
    }
}