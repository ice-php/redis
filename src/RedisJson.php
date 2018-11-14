<?php

declare(strict_types=1);

namespace icePHP;

/**
 * 字符串(String)类型
 */
class RedisJson extends RedisElement
{

    /**
     * 获取当前存储对象的类型(字符串格式)
     * @return string
     */
    public function getType(): string
    {
        return 'Json';
    }

    /**
     * 设置一个键值
     * @param $value string 值
     * @param bool $replace 是否覆盖
     * @param int $expire 生存期
     * @return bool 成功否
     */
    public function set($value, bool $replace = true, int $expire = 0): bool
    {
        return parent::setString(json($value), $replace, $expire);
    }

    /**
     * 获取当前缓存值,转换成原始数据类型
     * @return mixed
     */
    public function get()
    {
        //获取保存值
        $raw = parent::getRaw();
        if (is_null($raw)) {
            return null;
        }

        //JSON解码
        $decoded = json_decode($raw);

        //如果解码失败,返回原始内容
        if (is_null($decoded)) {
            return $raw;
        }

        return $decoded;
    }
}