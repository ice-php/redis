<?php
declare(strict_types=1);

namespace icePHP;
/**
 * 列表类型
 */
final class RedisList extends RedisElement
{
    //增加sort方法
    use RedisSortable;

    /**
     * 获取当前存储对象的类型(字符串格式)
     * @return string
     */
    public function getType(): string
    {
        return 'List';
    }

    /**
     * 在列表头部插入一个数据(这一个数据也可能是数组)
     * @param $value mixed 要插入的数据
     * @param bool $replace 是否覆盖
     * @return int|bool 命令后，列表的长度。
     */
    public function insert($value,bool $replace = true)
    {
        //覆盖
        if ($replace) {
            return $this->handle->lPush($this->key, $value);
        }

        //非覆盖
        return $this->handle->lPushx($this->key, $value);
    }

    /**
     * 将值value插入到列表key当中，位于值pivot之前。
     * @param $pivot mixed 支点
     * @param $value mixed 要插入的数据
     * @return int    如果命令执行成功，返回插入操作完成之后，列表的长度。如果没有找到pivot，返回-1。如果key不存在或为空列表，返回0。
     */
    public function insertBefore($pivot, $value): int
    {
        return $this->handle->lInsert($this->key, \redis::BEFORE, $pivot, $value);
    }

    /**
     * 将值value插入到列表key当中，位于值pivot之后。
     * @param $pivot mixed 支点
     * @param $value mixed 要插入的数据
     * @return int    如果命令执行成功，返回插入操作完成之后，列表的长度。如果没有找到pivot，返回-1。如果key不存在或为空列表，返回0。
     */
    public function insertAfter($pivot, $value): int
    {
        return $this->handle->lInsert($this->key, \redis::AFTER, $pivot, $value);
    }

    /**
     * 在列表头部插入多个数据
     * @param array $values 要插入的数据
     * @param bool $replace 是否覆盖
     * @return int 命令后，列表的长度。
     */
    public function insertMulti(array $values, bool $replace = true): int
    {
        array_unshift($values, $this->key);
        return call_user_func_array([$this->handle, $replace ? 'lPush' : 'lPushx'], $values);
    }

    /**
     * 将一个值value插入到列表key的表尾。
     * @param $value mixed 要插入的数据
     * @param bool $replace 是否覆盖
     * @return int 操作后，表的长度。
     */
    public function append($value, bool $replace = true): int
    {
        if ($replace) {
            return $this->handle->rPush($this->key, $value);
        }
        return $this->handle->rPushx($this->key, $value);
    }

    /**
     * 将多个值value插入到列表key的表尾。
     * @param $values array  要插入的数据
     * @param bool $replace 是否覆盖
     * @return int 操作后，表的长度。
     */
    public function appendMulti(array $values,bool $replace = true): int
    {
        array_unshift($values, $this->key);
        return call_user_func_array([$this->handle, $replace ? 'rPush' : 'rPushx'], $values);
    }

    /**
     * 移除并返回列表key的头元素。
     * @return string
     */
    public function popLeft(): string
    {
        return $this->handle->lPop($this->key);
    }

    /**
     * 移除并返回列表key的尾元素。
     * @return string
     */
    public function popRight(): string
    {
        return $this->handle->rPop($this->key);
    }

    /**
     * 阻塞式头部弹出
     * @return array
     */
    public function blockPopLeft(): array
    {
        return $this->handle->blPop([$this->key], $this->timeout);
    }

    /**
     * 阻塞式尾部弹出
     * @return array
     */
    public function blockPopRight()
    {
        return $this->handle->brPop([$this->key], $this->timeout);
    }

    /**
     * 返回列表key的长度。
     * @return int
     */
    public function length(): int
    {
        return $this->handle->lLen($this->key);
    }

    /**
     * 返回列表key中指定区间内的元素，区间以偏移量start和stop指定。
     * @param $start int 开始偏移量(0开始)
     * @param $stop int 结束偏移量(0开始)
     * @return array
     */
    public function getRange($start, $stop): array
    {
        return $this->handle->lRange($this->key, intval($start), intval($stop));
    }

    /**
     * 获取指定分页的数据
     * @param int $page 页码(1开始)
     * @param int $size 页长
     * @return array
     */
    public function page(int $page = 1, int $size = 20): array
    {
        return $this->getRange(($page - 1) * $size, $page * $size - 1);
    }

    /**
     * 从表头开始向表尾搜索，移除与value相等的元素，数量为count。
     * @param $value mixed 搜索的值
     * @param $count int 要删除的个数
     * @return int 被移除元素的数量。
     */
    public function removeFirst($value, int $count): int
    {
        return $this->handle->lRem($this->key, $value, $count);
    }

    /**
     * 从表尾开始向表头搜索，移除与value相等的元素，数量为count。
     * @param $value mixed 搜索的值
     * @param $count int 要删除的个数
     * @return int 被移除元素的数量。
     */
    public function removeLast($value,int $count): int
    {
        return $this->handle->lRem($this->key, $value, -$count);
    }

    /**
     *  移除表中所有与value相等的值。
     * @param $value mixed 搜索的值
     * @return int 被移除元素的数量。
     */
    public function removeAll($value): int
    {
        return $this->handle->lRem($this->key, $value, 0);
    }

    /**
     * 将列表key下标为index的元素的值设置为value。
     * @param $index int 索引(0开始)
     * @param $value mixed 要设置的值
     * @return BOOL 操作成功返回ok，否则返回错误信息
     */
    public function set(int $index, $value): bool
    {
        return $this->handle->lSet($this->key, intval($index), $value);
    }

    /**
     * 对一个列表进行修剪(trim)，就是说，让列表只保留指定区间内的元素，不在指定区间之内的元素都将被删除。
     * @param $start int 开始位置 0开始
     * @param $stop int  结束位置 0开始
     * @return array
     */
    public function trim(int $start, int $stop): array
    {
        return $this->handle->lTrim($this->key, intval($start), intval($stop));
    }

    /**
     * 返回列表key中，下标为index的元素。
     * @param $index int 索引 0开始
     * @return string
     */
    public function get(int $index): string
    {
        return $this->handle->lIndex($this->key, intval($index));
    }

    /**
     * 降级,最后一个元素移动到目标列表头部
     * @param $targetList string 目标列表键名
     * @return string 被移动的元素值
     */
    public function degrade(string $targetList): string
    {
        return $this->handle->rpoplpush($this->key, $targetList);
    }

    /**
     * 阻塞式降级,最后一个元素移动到目标列表头部
     * @param $target string 目标列表键名
     * @param int $timeout 超时秒数,0表示无限阻塞
     * @return string 被移动的元素值
     */
    public function moveBlock(string $target, int $timeout = 0): string
    {
        return $this->handle->brpoplpush($this->key, $target, $timeout);
    }
}
