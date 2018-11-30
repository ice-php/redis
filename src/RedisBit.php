<?php
declare(strict_types=1);

namespace icePHP;

/**
 * 位类型,String类型的子类型
 */
class RedisBit extends RedisElement
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
     * 对当前对象的存储值的二进制，设置或清除指定偏移量上的位(bit)。
     * @param $offset int  偏移(位)
     * @param $bit int 0/1
     * @return int 指定偏移量原来储存的位（"0"或"1"）.
     */
    public function setBit(int $offset, int $bit): int
    {
        return intval($this->handle->setBit($this->name, $offset, $bit));
    }

    /**
     * 对当前对象的存储值的二进制，获取指定偏移量上的位(bit)。
     * @param $offset  int  偏移(位)
     * @return int 字符串值指定偏移量上的位(bit)。
     */
    public function getBit(int $offset): int
    {
        return intval($this->handle->getBit($this->name, $offset));
    }

    /**
     * 统计当前对象的存储值的二进制被设置为1的bit数.
     * @return int
     */
    public function count(): int
    {
        return $this->handle->bitCount($this->name);
    }

    /**
     * 按位与,结果保存到当前对象
     * @param array $names 参与运算的多个对象名称
     * @return int 保存到 当前对象的存储值的二进制的(字符串)长度，和参数中对象中最长的二进制长度相等。
     */
    public function opAnd(array $names): int
    {
        return $this->handle->bitOp('AND', $this->name, ...$names);
    }

    /**
     * 按位或,结果保存到当前对象
     * @param array $names 参与运算的多个对象名称
     * @return int 保存到 当前对象的存储值的二进制的(字符串)长度，和参数中对象中最长的二进制长度相等。
     */
    public function opOr(array $names): int
    {
        return $this->handle->bitOp('OR', $this->name, ...$names);
    }

    /**
     * 按位异或,结果保存到当前对象
     * @param array $names 参与运算的多个对象名称
     * @return int 保存到 当前对象的存储值的二进制的(字符串)长度，和参数中对象中最长的二进制长度相等。
     */
    public function opXor(array $names): int
    {
        return $this->handle->bitOp('XOR', $this->name, ...$names);
    }

    /**
     * 按位非,结果保存到当前对象
     * @param string $source 源对象名称
     * @return int 保存到 当前对象的存储值的二进制的(字符串)长度，和源对象字符串长度相等。
     */
    public function opNot(string $source): int
    {
        return $this->handle->bitOp('NOT', $this->name, $source);
    }

    /**
     * 获取当前缓存值,以二进制存储方式
     * @return string
     */
    public function getRaw(): string
    {
        return parent::getRaw();
    }

    /**
     * 获取当前值,逐字节转换成ASCII码
     * @return array
     */
    public function getBytes(): array
    {
        return unpack('C*', $this->getRaw());
    }

    /**
     * 获取当前值,以十六进制串表示
     * @return string
     */
    public function getHex(): string
    {
        return unpack('H*', $this->getRaw())[1];
    }

    /**
     * 获取当前值,以二进制串表示
     * @return string
     */
    public function getBin(): string
    {
        return base_convert($this->getHex(), 16, 2);
    }

    /**
     * 设置一个值,以原始二进制格式
     * @param $value string 值
     * @param int $expire 生存期
     * @return bool 成功否
     */
    public function setRaw(string $value, int $expire = 0): bool
    {
        return parent::setString($value, $expire);
    }

    /**
     * 设置值,以字节ASCII数组格式
     * @param array $value
     * @param int $expire
     * @return bool
     */
    public function setBytes(array $value, int $expire = 0): bool
    {
        return $this->setRaw(pack('C*', $value), $expire);
    }

    /**
     * 设置值,以十六进制串格式
     * @param string $value
     * @param int $expire
     * @return bool
     */
    public function setHex(string $value, int $expire = 0): bool
    {
        return $this->setRaw(pack('H*', $value), $expire);
    }

    /**
     * 设置值,以二进制串格式
     * @param string $value
     * @param int $expire
     * @return bool
     */
    public function setBin(string $value, int $expire = 0): bool
    {
        return $this->setRaw(pack('H*', base_convert($value, 2, 16)), $expire);
    }
}