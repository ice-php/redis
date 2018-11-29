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
    public function inserts(array $members): int
    {
        return $this->handle->sAdd($this->key, ...$members);
    }

    /**
     * 移除集合key中的一个或多个member元素，不存在的member元素会被忽略。
     * @param array|string $members 要删除的元素
     * @return int 被成功移除的元素的数量，不包括被忽略的元素。
     */
    public function delete($members): int
    {
        if (!is_array($members)) {
            $members = [$members];
        }
        return $this->handle->sRem($this->key, ...$members);
    }

    /**
     * 返回集合key中的所有成员。
     * @return array
     */
    public function all(): array
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
    public function count(): int
    {
        return $this->handle->sCard($this->key);
    }

    /**
     * 将member元素从source集合移动到destination集合。
     * @param string $target 目标集体名称
     * @param $member mixed 要移动的元素
     * @return bool 如果member元素被成功移除，返回1。
     */
    public function move($target, $member): bool
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
     * @param $sets array 集合名称数组
     * @return int 结果集中的成员数量。
     */
    public function inter(array $sets): int
    {
        return $this->handle->sInterStore($this->key, ...$sets);
    }

    /**
     * 并集,保存到当前对象,如果有则覆盖
     * @param $sets array 集合名称数组
     * @return int 结果集中的成员数量。
     */
    public function union(array $sets): int
    {
        return $this->handle->sUnionStore($this->key, ...$sets);
    }

    /**
     * 并集,返回结果集合
     * @param array $sets 集合名称数组
     * @return array
     */
    public function unionResult(array $sets): array
    {
        return $this->handle->sUnion(...$sets);
    }

    /**
     * 交集,返回结果集合
     * @param array $sets 集合名称数组
     * @return array
     */
    public function interResult(array $sets): array
    {
        return $this->handle->sInter(...$sets);
    }

    /**
     * 差集,保存到当前对象,如果有则覆盖
     * @param $sets array 集合名称数组
     * @return int 结果集中的成员数量。
     */
    public function diff(array $sets): int
    {
        return $this->handle->sDiffStore($this->key, ...$sets);
    }


    /**
     * 匹配查询,无法分页
     * @param string $pattern 通配符,可以使用* 表示 任意多个字符,?表示任意一个字符, [abc]表示其中a或者b或者c
     * @return \Iterator
     */
    public function select(string $pattern): \Iterator
    {
        //游标,每次查询必须从0开始
        $iterator = NULL;
        while (true) {
            //查询并更新游标
            $ret = $this->handle->sScan($this->key, $iterator, $pattern, 10);

            //没有更多数据了
            if (false === $ret) {
                break;
            }

            //逐个返回
            foreach ($ret as $val) {
                yield  $val;
            }
        }
    }
}
