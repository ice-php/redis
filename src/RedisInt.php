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
     * @throws RedisException
     */
    public function crease(int $diff = 1): int
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
        throw new RedisException('数值增减方法的参数错误:'.$diff,RedisException::PARAM_ERROR_FOR_CREASE);
    }


    /**
     * 获取当前缓存值,转换成整数
     * @return int
     */
    public function get(): int
    {
        return intval(parent::getRaw());
    }

    /**
     * 设置一个键值
     * @param $value int 值
     * @param bool $replace 是否覆盖
     * @param int $expire 生存期
     * @return bool 成功否
     */
    public function set(int $value,bool $replace = true, int $expire = 0):bool
    {
        $handle = $this->handle;

        //如果允许覆盖
        if ($replace) {
            //覆盖并设置生存时间
            if ($expire) {
                return $handle->setex($this->key, $expire, $value);
            }

            //仅覆盖
            return $handle->set($this->key, $value);
        }

        //不允许覆盖
        $ret = $handle->setnx($this->key, $value);

        //存储失败
        if (!$ret) {
            return $ret;
        }

        //如果要求设置生存时间
        if ($expire) {
            $this->setExpire($expire);
        }

        return true;
    }
}