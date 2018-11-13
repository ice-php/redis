<?php
declare(strict_types=1);

namespace icePHP;

/**
 * 位类型,String类型的子类型
 */
final class RedisBit extends RedisElement
{
    /**
     * 获取当前存储对象的类型(字符串格式)
     * @return string
     */
    public function getType(): string
    {
        return 'Bit';
    }

    /**
     * 对key所储存的字符串值，设置或清除指定偏移量上的位(bit)。
     * @param $offset int  偏移(位)
     * @param $bit int 0/1
     * @return int 指定偏移量原来储存的位（"0"或"1"）.
     */
    public function setBit(int $offset, int $bit): int
    {
        return intval($this->handle->setBit($this->key, intval($offset), intval($bit)));
    }

    /**
     * 对key所储存的字符串值，获取指定偏移量上的位(bit)。
     * @param $offset  int  偏移(位)
     * @return int 字符串值指定偏移量上的位(bit)。
     */
    public function getBit(int $offset): int
    {
        return intval($this->handle->getBit($this->key, intval($offset)));
    }

    /**
     * 统计字符串被设置为1的bit数.
     * @return int
     */
    public function count(): int
    {
        return $this->handle->bitCount($this->key);
    }

    /**
     * 按位与,结果保存到当前对象
     * @return int 保存到 destkey 的字符串的长度，和输入 key 中最长的字符串长度相等。
     */
    public function opAnd(): int
    {
        $argv = func_get_args();
        array_unshift($argv, 'AND', $this->key);
        return call_user_func_array([$this->handle, 'bitOp'], $argv);
    }

    /**
     * 按位或,结果保存到当前对象
     * @return int 保存到 destkey 的字符串的长度，和输入 key 中最长的字符串长度相等。
     */
    public function opOr(): int
    {
        $argv = func_get_args();
        array_unshift($argv, 'OR', $this->key);
        return call_user_func_array([$this->handle, 'bitOp'], $argv);
    }

    /**
     * 按位异或,结果保存到当前对象
     * @return int 保存到 destkey 的字符串的长度，和输入 key 中最长的字符串长度相等。
     */
    public function opXor(): int
    {
        $argv = func_get_args();
        array_unshift($argv, 'XOR', $this->key);
        return call_user_func_array([$this->handle, 'bitOp'], $argv);
    }

    /**
     * 按位非,结果保存到指定对象
     * @param string $target 目标对象名称 ,默认为本对象
     * @return int 保存到 destkey 的字符串的长度，和输入 key 中最长的字符串长度相等。
     */
    public function opNot(?string $target = null): int
    {
        return $this->handle->bitOp('NOT', $target ?: $this->key, $this->key);
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