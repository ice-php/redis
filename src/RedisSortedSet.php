<?php
declare(strict_types=1);

namespace icePHP;
/**
 * 有序集合
 */
final class RedisSortedSet extends RedisElement
{
    //增加sort方法
    use RedisSortable;

    /**
     * 获取当前存储对象的类型(字符串格式)
     * @return string
     */
    public function getType(): string
    {
        return 'SortedSet';
    }

    /**
     * 将一个member元素及其score值加入到有序集key当中。
     * @param $score float 排序值
     * @param $member mixed 要保存的元素
     * @return int 被成功添加的新成员的数量，不包括那些被更新的、已经存在的成员。
     */
    public function add(float $score, $member): int
    {
        return $this->handle->zAdd($this->key, $score, $member);
    }

    /**
     * 将多个member元素及其score值加入到有序集key当中
     * @param array $elements 键/值
     * @return int 被成功添加的新成员的数量，不包括那些被更新的、已经存在的成员。
     */
    public function addMulti(array $elements): int
    {
        $params = [$this->key];
        foreach ($elements as $k => $v) {
            $params[] = $k;
            $params[] = $v;
        }
        return call_user_func_array([$this->handle, 'zAdd'], $params);
    }

    /**
     * 移除有序集key中的一个或多个成员，不存在的成员将被忽略。
     * 参数为多个元素(不是排序值)
     * @return int 被成功移除的成员的数量，不包括被忽略的成员。
     */
    public function remove(): int
    {
        $argv = func_get_args();
        array_unshift($argv, $this->key);
        return call_user_func_array([$this->handle, 'zRem'], $argv);
    }

    /**
     * 返回有序集key的基数(即元素的个数)
     * @return int
     */
    public function length(): int
    {
        return $this->handle->zCard($this->key);
    }

    /**
     * 返回有序集key中，score值在min和max之间(默认包括score值等于min或max)的成员数量
     * @param $min float 排序值
     * @param $max float 排序值
     * @return int 元素个数
     */
    public function count(float $min, float $max): int
    {
        return $this->handle->zCount($this->key, $min, $max);
    }

    /**
     * 返回有序集key中，成员member的score值。
     * @param $member mixed 元素
     * @return float 排序值
     */
    public function getScore($member): float
    {
        return $this->handle->zScore($this->key, $member);
    }

    /**
     * 为有序集key的成员member的score值加上增量increment。
     * @param $member mixed 元素
     * @param int $diff 增量,可以是负值
     * @return float member成员的新score值，以字符串形式表示。
     */
    public function crease($member, float $diff = 1): float
    {
        return $this->handle->zIncrBy($this->key, $diff, $member);
    }

    /**
     * 返回有序集key中，指定区间内的成员。* 根据元素的值判断
     * @param $start float 排序值
     * @param $stop float 排序值
     * @param bool $withScore 是否将score一起返回(score作为值)
     * @param bool $desc 是否降序排列
     * @return array
     */
    public function range(float $start, float $stop, bool $withScore = true, bool $desc = false): array
    {
        //按降序获取
        if ($desc) {
            return $this->handle->zRevRange($this->key, max($start, $stop), min($start, $stop), $withScore);
        }

        //按升序获取
        return $this->handle->zRange($this->key, min($start, $stop), max($start, $stop), $withScore);
    }

    /**
     * 返回所有数据
     * @return array
     */
    public function get(): array
    {
        return $this->range(0, -1, true);
    }

    /**
     * 返回有序集key中，所有score值介于min和max之间(包括等于min或max)的成员。
     * @param $min float 排序值
     * @param $max float 排序值
     * @param null $limit 分页(以1开始) 可能是数组
     * @param bool $withScores 是否附带返回score值
     * @param bool $desc 是否降序排列
     * @return array
     */
    public function rangeByScore(float $min, float $max, $limit = null, bool $withScores = true, bool $desc = false): array
    {
        //组装 参数数组
        $options = [];
        if ($withScores) {
            $options['withscores'] = true;
        }
        if ($limit) {
            $options['limit'] = $limit;
        }


        $miner = min($min, $max);
        $maxer = max($min, $max);
        //如果降序
        if ($desc) {
            [$begin, $end] = [$miner, $maxer];
        } else {
            [$begin, $end] = [$maxer, $miner];
        }
        return $this->handle->zRevRangeByScore($this->key, strval($begin), strval($end), $options);
    }

    /**
     * 返回有序集key中成员member的排名。其中有序集成员按score值递增(从小到大)顺序排列。
     * @param $member mixed 元素
     * @param $desc bool 是否降序排列
     * @return int 排名
     */
    public function rank($member, bool $desc = false): int
    {
        //获取降序排名
        if ($desc) {
            return $this->handle->zRevRank($this->key, $member);
        }

        //获取升序排名
        return $this->handle->zRank($this->key, $member);
    }

    /**
     * 移除有序集key中，指定排名(rank)区间内的所有成员。
     * @param $min  int 排名
     * @param $max int 排名
     * @return int 被移除成员的数量。
     */
    public function removeByRank(int $min, int $max): int
    {
        return $this->handle->zRemRangeByRank($this->key, intval($min), intval($max));
    }

    /**
     * 移除有序集key中，所有score值介于min和max之间(包括等于min或max)的成员。
     * @param $min float 排序值
     * @param $max float 排序值
     * @return int 被移除成员的数量。
     */
    public function removeByScore(float $min, float $max): int
    {
        return $this->handle->zRemRangeByScore($this->key, $min, $max);
    }

    /**
     * 计算给定的一个或多个有序集的交集
     * 使用WEIGHTS选项，你可以为每个给定有序集分别指定一个乘法因子(multiplication factor)，每个给定有序集的所有成员的score值在传递给聚合函数(aggregation function)之前都要先乘以该有序集的因子。
     * 如果没有指定WEIGHTS选项，乘法因子默认设置为1。
     * @param array $sets 参与计算的集合
     * @param array|null $weights 乘法因子
     * @param string $aggregate 聚合:SUM/min/max
     * @return int
     */
    public function inter(array $sets, array $weights = null, string $aggregate = 'SUM'): int
    {
        return $this->handle->zInter($this->key, $sets, $weights, $aggregate);
    }

    /**
     * 计算给定的一个或多个有序集的并集
     * 使用WEIGHTS选项，你可以为每个给定有序集分别指定一个乘法因子(multiplication factor)，每个给定有序集的所有成员的score值在传递给聚合函数(aggregation function)之前都要先乘以该有序集的因子。
     * 如果没有指定WEIGHTS选项，乘法因子默认设置为1。
     * @param array $sets 参与计算的集合
     * @param array|null $weights 乘法因子
     * @param string $aggregate 聚合:SUM/min/max
     * @return int
     */
    public function union(array $sets, array $weights = null, string $aggregate = 'SUM'): int
    {
        return $this->handle->zUnion($this->key, $sets, $weights, $aggregate);
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
        return $this->handle->zScan($this->key, $iterator, $pattern, $count);
    }
}