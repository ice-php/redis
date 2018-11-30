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
     * 从当前列表头部获取一个元素,如果没有则,等待
     * @param $timeout int 超时限制
     * @return array 超时会返回空数组
     */
    public function popLeftWait(int $timeout = 0): array
    {
        return $this->handle->blPop([$this->name], $timeout);
    }

    /**
     * 从当前列表尾部获取一个元素,如果没有则等待
     * @param $timeout int 超时限制
     * @return array 超时会返回空数组
     */
    public function popRightWait(int $timeout = 0): array
    {
        return $this->handle->brPop([$this->name], $timeout);
    }

    /**
     * 从当前列表尾部获取一个元素,并移动到目标列表头部,如果没有则等待
     * @param $target string 目标列表键名
     * @param int $timeout 超时秒数,0表示无限阻塞
     * @return string 被移动的元素值
     */
    public function moveWait(string $target, int $timeout = 0): string
    {
        return $this->handle->brpoplpush($this->name, $target, $timeout);
    }

    /**
     * 返回列表key中，下标为index的元素。
     * @param $index int 索引 0开始
     * @return string
     */
    public function get(int $index): string
    {
        return $this->handle->lIndex($this->name, $index);
    }

    /**
     * 将值value插入到列表key当中，位于值pivot之前。
     * @param $pivot mixed 支点(列表中存在的元素)
     * @param $value mixed 要插入的数据
     * @return int    如果命令执行成功，返回插入操作完成之后，列表的长度。如果没有找到pivot，返回-1。如果key不存在或为空列表，返回0。
     */
    public function insertBefore($pivot, $value): int
    {
        return $this->handle->lInsert($this->name, \redis::BEFORE, $pivot, $value);
    }

    /**
     * 将值value插入到列表key当中，位于值pivot之后。
     * @param $pivot mixed 支点
     * @param $value mixed 要插入的数据
     * @return int    如果命令执行成功，返回插入操作完成之后，列表的长度。如果没有找到pivot，返回-1。如果key不存在或为空列表，返回0。
     */
    public function insertAfter($pivot, $value): int
    {
        return $this->handle->lInsert($this->name, \redis::AFTER, $pivot, $value);
    }

    /**
     * 返回列表key的长度(其中元素的个数)。
     * @return int
     */
    public function count(): int
    {
        return $this->handle->lLen($this->name);
    }

    /**
     * 移除并返回列表key的头元素。
     * @return string 如果空列表,则返回 null
     */
    public function popLeft(): ?string
    {
        $ret = $this->handle->lPop($this->name);
        if (false === $ret) {
            return null;
        }
        return $ret;
    }

    /**
     * 在列表头部插入一个元素(这一个数据也可能是数组)
     * @param $member mixed 要插入的元素
     * @return int 命令后，列表的长度。
     */
    public function insert($member)
    {
        //非覆盖
        return $this->handle->lPush($this->name, $member);
    }

    /**
     * 在列表头部插入多个元素(从左到右)
     * @param array $members
     * @return int
     */
    public function inserts(array $members)
    {
        return $this->handle->lPush($this->name, ...$members);
    }

    /**
     * 返回列表key中指定区间内的元素，区间以偏移量start和stop指定。(0表示第一个,-1表示最后一个
     * @param $start int 开始偏移量(0开始)
     * @param $stop int 结束偏移量(0开始)
     * @return array
     */
    public function select(int $start, int $stop): array
    {
        return $this->handle->lRange($this->name, intval($start), intval($stop));
    }

    /**
     * 获取指定分页的数据
     * @param int $page 页码(1开始)
     * @param int $size 页长
     * @return array
     */
    public function page(int $page = 1, int $size = 20): array
    {
        return $this->select(($page - 1) * $size, $page * $size - 1);
    }

    /**
     * 从表头开始向表尾搜索，移除与value相等的元素，数量为count。
     * @param $value mixed 搜索的值
     * @param $count int 要删除的个数
     * @return int 被移除元素的数量。
     */
    public function deleteFirst($value, int $count = 1): int
    {
        return $this->handle->lRem($this->name, $value, $count);
    }

    /**
     * 从表尾开始向表头搜索，移除与value相等的元素，数量为count。
     * @param $value mixed 搜索的值
     * @param $count int 要删除的个数
     * @return int 被移除元素的数量。
     */
    public function deleteLast($value, int $count = 1): int
    {
        return $this->handle->lRem($this->name, $value, -$count);
    }

    /**
     *  移除表中所有与value相等的值。
     * @param $value mixed 搜索的值
     * @return int 被移除元素的数量。
     */
    public function deleteAll($value): int
    {
        return $this->handle->lRem($this->name, $value, 0);
    }

    /**
     * 将列表key下标为index的元素的值设置为value。
     * @param $index int 索引(0开始)
     * @param $value mixed 要设置的值
     * @return BOOL 操作成功返回ok，否则返回错误信息
     */
    public function update(int $index, $value): bool
    {
        return $this->handle->lSet($this->name, $index, $value);
    }

    /**
     * 对一个列表进行修剪(trim)，就是说，让列表只保留指定区间内的元素，不在指定区间之内的元素都将被删除。
     * @param $start int 开始位置 0开始,-1表示最后一个
     * @param $stop int  结束位置 0开始
     * @return array
     */
    public function trim(int $start, int $stop): array
    {
        return $this->handle->lTrim($this->name, $start, $stop);
    }

    /**
     * 移除并返回列表key的尾元素。
     * @return string
     */
    public function popRight(): ?string
    {
        $ret = $this->handle->rPop($this->name);
        return $ret === false ? null : $ret;
    }

    /**
     * 将本表尾部(右)的最后一个元素移动到目标列表的头部
     * @param string $target 目标列表名称
     * @return string 被 移动 的元素, 如果失败则为NULL
     */
    public function moveTo(string $target): ?string
    {
        $ret = $this->handle->rpoplpush($this->name, $target);
        return (false === $ret) ? null : $ret;
    }

    /**
     * 将源列表的尾部(右)的最后一个元素移动到当前列表的头部
     * @param string $source 源列表名称
     * @return null|string 被 移动的元素,失败则为NULL
     */
    public function moveFrom(string $source): ?string
    {
        $ret = $this->handle->rpoplpush($source, $this->name);
        return (false === $ret) ? null : $ret;
    }

    /**
     * 将一个值value插入到列表key的表尾(右)。
     * @param $value mixed 要插入的数据
     * @return int 操作后，表的长度。
     */
    public function append($value): int
    {
        return $this->handle->rPush($this->name, $value);
    }

    /**
     * 将多个值value插入到列表key的表尾。
     * @param $values array  要插入的数据
     * @return int 操作后，表的长度。
     */
    public function appendMulti(array $values): int
    {
        return $this->handle->rPush($this->name,...$values);
    }
}
