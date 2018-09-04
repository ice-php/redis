<?php
declare(strict_types=1);

namespace icePHP;

/**
 * 字符串(String)类型
 */
class RedisString extends RedisElement
{
    /**
     * 设置一个键值
     * @param $value mixed 值
     * @param bool $replace 是否覆盖
     * @param int $expire 生存期
     * @return $this|bool
     */
    public function set($value, $replace = true, $expire = 0)
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

        return $this;
    }

    /**
     * 获取当前存储对象的类型(字符串格式)
     * @return string
     */
    public function getType(): string
    {
        return 'String';
    }

    /**
     * 用value参数覆写(Overwrite)给定key所储存的字符串值，从偏移量offset开始
     * @param $offset int 偏移
     * @param $value string 填充内容
     * @return int 被SETRANGE修改之后，字符串的长度。
     */
    public function setRange($offset, $value): int
    {
        return intval($this->handle->setRange($this->key, $offset, $value));
    }

    /**
     * 将value追加到key原来的值之后
     * @param $value string 要追加的字符串
     * @return int 完成后的字符串长度
     */
    public function append($value): int
    {
        return intval($this->handle->append($this->key, $value));
    }

    /**
     * 返回当前存储对象所关联的字符串值
     * @return string
     */
    public function get()
    {
        return $this->handle->get($this->key);
    }

    /**
     * 返回key中字符串值的子字符串，字符串的截取范围由start和end两个偏移量决定(包括start和end在内)。
     * 负数偏移量表示从字符串最后开始计数，-1表示最后一个字符，-2表示倒数第二个，以此类推。
     * @param $start int
     * @param $end int
     * @return string
     */
    public function getRange($start, $end): string
    {
        return $this->handle->getRange($this->key, $start, $end);
    }

    /**
     * 将给定key的值设为value，并返回key的旧值。
     * @param $value string 新值
     * @return string 原值
     */
    public function getSet($value): string
    {
        return $this->handle->getSet($this->key, $value);
    }

    /**
     * 返回key所储存的字符串值的长度。
     * @return int
     */
    public function length(): int
    {
        return $this->handle->strlen($this->key);
    }

    /**
     * 转换成BIT类型
     * @return RedisBit
     */
    public function toBit(): RedisBit
    {
        return new RedisBit($this->handle, $this->key);
    }

    /**
     * 转换成Int类型
     * @return RedisInt
     */
    public function toInt(): RedisInt
    {
        return new RedisInt($this->handle, $this->key);
    }

    /**
     * 转换成Float类型
     * @return RedisFloat
     */
    public function toFloat(): RedisFloat
    {
        return new RedisFloat($this->handle, $this->key);
    }
}