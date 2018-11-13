<?php
declare(strict_types=1);

namespace icePHP;
/**
 * 集合类型存储类
 */
final class RedisSet extends RedisElement
{
    //增加sort方法
    use RedisSortable;

    /**
     * 获取当前存储对象的类型(字符串格式)
     * @return string
     */
    public function getType(): string
    {
        return 'Set';
    }

    /**
     * 向集合中添加一个元素
     * @param $member mixed 要添加的元素
     * @return int 被添加到集合中的新元素的数量，不包括被忽略的元素。
     */
    public function add($member): int
    {
        return $this->handle->sAdd($this->key, $member);
    }

    /**
     * 向集合中添加多个元素
     * @param array $members 要添加的元素数组
     * @return int 被添加到集合中的新元素的数量，不包括被忽略的元素。
     */
    public function addMulti(array $members): int
    {
        array_unshift($members, $this->key);
        return call_user_func_array([$this->handle, 'sAdd'], $members);
    }

    /**
     * 移除集合key中的一个或多个member元素，不存在的member元素会被忽略。
     * @return int 被成功移除的元素的数量，不包括被忽略的元素。
     */
    public function remove(): int
    {
        $argv = func_get_args();
        array_unshift($argv, $this->key);
        return call_user_func_array([$this->handle, 'sRem'], $argv);
    }

    /**
     * 返回集合key中的所有成员。
     * @return array
     */
    public function get(): array
    {
        return $this->handle->sMembers($this->key);
    }

    /**
     * 判断member元素是否是集合key的成员。
     * @param $member string 元素
     * @return bool
     */
    public function exists($member): bool
    {
        return $this->handle->sIsMember($this->key, $member);
    }

    /**
     * 返回集合key的基数(集合中元素的数量)。
     * @return int
     */
    public function length(): int
    {
        return $this->handle->sCard($this->key);
    }

    /**
     * 将member元素从source集合移动到destination集合。
     * @param string $target 目标集体名称
     * @param $member mixed 要移动的元素
     * @return bool 如果member元素被成功移除，返回1。
     */
    public function degrade($target, $member): bool
    {
        return $this->handle->sMove($this->key, $target, $member);
    }

    /**
     * 移除并返回集合中的一个随机元素。
     * @return string
     */
    public function popRandom(): string
    {
        return $this->handle->sPop($this->key);
    }

    /**
     * 返回集合中的一个随机元素。
     * @return string
     */
    public function getRandom(): string
    {
        return $this->handle->sRandMember($this->key);
    }

    /**
     * 计算参数中给定的集合的交集并保存到当前集合对象 , 如果有则覆盖
     * @return int 结果集中的成员数量。
     */
    public function inter(): int
    {
        $argv = func_get_args();
        array_unshift($argv, $this->key);
        return call_user_func_array([$this->handle, 'sInterStore'], $argv);
    }

    /**
     * 并集,保存到当前对象,如果有则覆盖
     * @return int 结果集中的成员数量。
     */
    public function union(): int
    {
        $argv = func_get_args();
        array_unshift($argv, $this->key);
        return call_user_func_array([$this->handle, 'sUnionStore'], $argv);
    }

    /**
     * 差集,保存到当前对象,如果有则覆盖
     * @return int 结果集中的成员数量。
     */
    public function diff(): int
    {
        $argv = func_get_args();
        array_unshift($argv, $this->key);
        return call_user_func_array([$this->handle, 'sDiffStore'], $argv);
    }

    /**
     * 从当前游标开始访问指定数量的元素
     * @param int $iterator 游标(最初以0开始)
     * @param string $pattern 匹配
     * @param int $count 返回数量
     * @return array|bool 返回的新游标和元素,如果新的游标为0,表示结束
     */
    public function scan(int $iterator = 0, string $pattern = '', int $count = 0)
    {
        return $this->handle->sScan($this->key, $iterator, $pattern, $count);
    }
}
