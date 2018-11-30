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
     * 向有序集合中添加一个键值对
     * @param $key string 键,唯一
     * @param $val float 值,只允许整数/浮点
     * @return int 被成功添加的新成员的数量，不包括那些被更新的、已经存在的成员。
     */
    public function insert(string $key, float $val): int
    {
        return $this->handle->zAdd($this->name, $val, $key);
    }

    /**
     * 插入多个键值对(键是唯一的)
     * @param array $kvs [键(字符串)=>值(只允许整数/浮点)]
     * @return int 被成功添加的新成员的数量，不包括那些被更新的、已经存在的成员。
     */
    public function inserts(array $kvs): int
    {
        $params = [];
        foreach ($kvs as $key => $val) {
            $params[] = $val; //分数(值)
            $params[] = $key; //元素(键)
        }
        return $this->handle->zAdd($this->name, ...$params);
    }

    /**
     * 返回有序集合中,元素的个数(基数)
     * @return int
     */
    public function count(): int
    {
        return $this->handle->zCard($this->name);
    }

    /**
     * 返回集合中，值(分数)在min和max之间(默认包括值等于min或max)的成员数量
     * @param $min mixed 排序值
     * @param $max mixed 排序值
     * @return int 元素个数
     */
    public function countByValue($min = '-inf', $max = '+inf'): int
    {
        return $this->handle->zCount($this->name, strval($min), strval($max));
    }

    /**
     * 根据元素的值,对键进行增加
     * @param $key string 键(元素)
     * @param float $diff 值(分数)增量,可以是负值
     * @return float 变化后的值(分数)
     */
    public function increase(string $key, float $diff = 1): float
    {
        return $this->handle->zIncrBy($this->name, $diff, $key);
    }

    /**
     * 根据元素的值,对键进行减少
     * @param $key string 键(元素)
     * @param float $diff 值(分数)减量,可以是负值
     * @return float 变化后的值(分数)
     */
    public function decrease(string $key, float $diff = 1): float
    {
        return $this->increase($key, -$diff);
    }

    /**
     * 根据元素的排序值(不是分数)返回有序集key中，指定区间内的成员键(元素)。
     * @param $start int 排序值 起点(0表示第一个)  (0,-1)表示全部 (2,-2)表示第3个至倒数第二个, (-2,2)表示最后两个
     * @param $stop int 排序值 终点(负数表示倒数)
     * @param bool $desc 是否降序排列
     * @return array 注意:  键(元素)=>值(分数)  或者 是纯键数组
     */
    public function selectKeysByRank(int $start = 0, int $stop = -1, bool $desc = false): array
    {
        //按降序获取
        if ($desc) {
            return $this->handle->zRevRange($this->name, $start, $stop, false);
        }

        //按升序获取
        return $this->handle->zRange($this->name, $start, $stop, false);
    }

    /**
     * 根据元素的排序值(不是分数)返回有序集key中，指定区间内的成员(键=>值,元素=>分数)。
     * @param $start int 排序值 起点(0表示第一个)  (0,-1)表示全部 (2,-2)表示第3个至倒数第二个, (-2,2)表示最后两个
     * @param $stop int 排序值 终点(负数表示倒数)
     * @param bool $desc 是否降序排列
     * @return array 注意:  键(元素)=>值(分数)  或者 是纯键数组
     */
    public function selectByRank(int $start = 0, int $stop = -1, bool $desc = false): array
    {
        //按降序获取
        if ($desc) {
            return $this->handle->zRevRange($this->name, $start, $stop, true);
        }

        //按升序获取
        return $this->handle->zRange($this->name, $start, $stop, true);
    }

    /**
     * 根据值(分数),返回[min,max]之间的元素(键)
     * @param $min float|string 值(分数)(-inf表示无下限,'(m'表示开区间)
     * @param $max float|string 值(分数)(+inf表示无上限,'(m'表示开区间)
     * @param array $limit 分页 [offset,length] 0 开始
     * @param bool $desc 是否降序排列
     * @return array
     */
    public function selectKeysByValue($min = '-inf', $max = '+inf',array $limit = null, bool $desc = false): array
    {
        //组装 参数数组
        $options = ['withscores' => false];

        if ($limit) {
            $options['limit'] = $limit;
        }

        $min = strval($min);
        $max = strval($max);
        //如果降序
        if ($desc) {
            return $this->handle->zRevRangeByScore($this->name, $min, $max, $options);
        } else {
            return $this->handle->zRangeByScore($this->name, $min, $max, $options);
        }
    }

    /**
     * 根据值(分数),返回[min,max]之间的数据(元素=>分数,键=>值)
     * @param $min float|string 值(分数)(-inf表示无下限,'(m'表示开区间)
     * @param $max float|string 值(分数)(+inf表示无上限,'(m'表示开区间)
     * @param array $limit 分页 [offset,length] 0 开始
     * @param bool $desc 是否降序排列
     * @return array
     */
    public function selectByValue($min = '-inf', $max = '+inf', array $limit = null, bool $desc = false): array
    {
        //组装 参数数组
        $options = ['withscores' => true];

        if ($limit) {
            $options['limit'] = $limit;
        }

        $min = strval($min);
        $max = strval($max);
        //如果降序
        if ($desc) {
            return $this->handle->zRevRangeByScore($this->name, $min, $max, $options);
        } else {
            return $this->handle->zRangeByScore($this->name, $min, $max, $options);
        }
    }

    /**
     * 返回指定键(元素)在有序集合中的排名,0表示第一
     * @param $key string 键(元素)
     * @param $desc bool 是否降序排列
     * @return int 排名
     */
    public function getRank(string $key, bool $desc = false): ?int
    {
        //获取降序排名
        if ($desc) {
            return $this->handle->zRevRank($this->name, $key);
        }

        //获取升序排名
        return $this->handle->zRank($this->name, $key);
    }

    /**
     * 移除一个或多个成员，不存在的成员将被忽略。
     * @param $keys array|string ,是元素值,不是分数值
     * @return int 被成功移除的成员的数量，不包括被忽略的成员。
     */
    public function delete($keys): int
    {
        if (!is_array($keys)) {
            $keys = [$keys];
        }
        return $this->handle->zRem($this->name, ...$keys);
    }

    /**
     * 根据排名,删除指定闭区间内的元素 [min,max]
     * @param $min  int 排名  -1表示倒数第1,0表示第1
     * @param $max int 排名
     * @return int 被移除成员的数量。
     */
    public function deleteByRank(int $min, int $max): int
    {
        return $this->handle->zRemRangeByRank($this->name, $min , $max );
    }

    /**
     * 根据值(分数),删除指定区间内的元素
     * @param $min string|float 值(分数),-inf表示负无穷,+inf表示正无穷,'(m' 表示开区间
     * @param $max string|float 值(分数),-inf表示负无穷,+inf表示正无穷,'(m' 表示开区间
     * @return int 被移除成员的数量。
     */
    public function deleteByValue($min, $max): int
    {
        return $this->handle->zRemRangeByScore($this->name, $min, $max);
    }

    /**
     * 根据键,获取值
     * @param $key string 键(元素)
     * @return float 值(分数)
     */
    public function getValue(string $key): float
    {
        return $this->handle->zScore($this->name, $key);
    }

    /**
     * 计算指定集合的并集保存到当前集合中,结果元素的分数为每个集合中的同一元素分数的合计|最小值|最大值
     * @param array $sets 集合名称数组
     * @param array|null $weights 权重数组
     * @param string $aggregate 聚合函数(SUM/MIN/MAX)
     * @return int 结果集元素个数
     */
    private function unionAggregate(array $sets, array $weights = null, string $aggregate = 'SUM'): int
    {
        return $this->handle->zUnion($this->name, $sets, $weights, $aggregate);
    }

    /**
     * 计算指定集合的并集保存到当前集合中,结果元素的分数为每个集合中的同一元素分数的合计
     * @param array $sets 集合名称数组
     * @param array|null $weights 权重数组
     * @return int 结果集元素个数
     */
    public function unionSum(array $sets, array $weights = null)
    {
        return $this->unionAggregate($sets, $weights, 'SUM');
    }

    /**
     * 计算指定集合的并集,结果元素的分数为每个集合中的同一元素分数的最小值
     * @param array $sets 集合名称数组
     * @param array|null $weights 权重数组
     * @return int 结果集元素个数
     */
    public function unionMin(array $sets, array $weights = null)
    {
        return $this->unionAggregate($sets, $weights, 'MIN');
    }

    /**
     * 计算指定集合的并集,结果元素的分数为每个集合中的同一元素分数的最大值
     * @param array $sets 集合名称数组
     * @param array|null $weights 权重数组
     * @return int 结果集元素个数
     */
    public function unionMax(array $sets, array $weights = null)
    {
        return $this->unionAggregate($sets, $weights, 'MAX');
    }

    /**
     * 计算给定的多个有序集合的并集的结果(只获取元素)
     * @param array $sets 参与计算的集合
     * @return array 结果集合元素数组
     */
    public function union(array $sets): array
    {
        $this->unionSum($sets);
        return array_keys($this->all());
    }

    /**
     * 计算指定集合的交集,结果元素的分数为每个集合中的同一元素分数的合计|最小值|最大值
     * @param array $sets 集合名称数组
     * @param array|null $weights 权重数组
     * @param string $aggregate 聚合函数(SUM/MIN/MAX)
     * @return int 结果集元素个数
     */
    private function interAggregate(array $sets, array $weights = null, string $aggregate = 'SUM'): int
    {
        return $this->handle->zInter($this->name, $sets, $weights, $aggregate);
    }

    /**
     * 计算指定集合的交集,结果元素的分数为每个集合中的同一元素分数的合计
     * @param array $sets 集合名称数组
     * @param array|null $weights 权重数组
     * @return int 结果集元素个数
     */
    public function interSum(array $sets, array $weights = null): int
    {
        return $this->interAggregate($sets, $weights, 'SUM');
    }

    /**
     * 计算指定集合的交集,结果元素的分数为每个集合中的同一元素分数的最小值
     * @param array $sets 集合名称数组
     * @param array|null $weights 权重数组
     * @return int 结果集元素个数
     */
    public function interMin(array $sets, array $weights = null): int
    {
        return $this->interAggregate($sets, $weights, 'MIN');
    }

    /**
     * 计算指定集合的交集,结果元素的分数为每个集合中的同一元素分数的最大值
     * @param array $sets 集合名称数组
     * @param array|null $weights 权重数组
     * @return int 结果集元素个数
     */
    public function interMax(array $sets, array $weights = null): int
    {
        return $this->interAggregate($sets, $weights, 'MAX');
    }

    /**
     * 计算给定的多个有序集合的交集的结果(只获取元素)
     * @param array $sets 参与计算的集合
     * @return array 结果元素数组
     */
    public function inter(array $sets): array
    {
        $this->interSum($sets);
        return array_keys($this->all());
    }

    /**
     * 匹配查询(匹配键(元素)),无法分页
     * @param string $pattern 通配符,可以使用* 表示 任意多个字符,?表示任意一个字符, [abc]表示其中a或者b或者c
     * @return \Iterator
     */
    public function select(string $pattern): \Iterator
    {
        //游标,每次查询必须从0开始
        $iterator = NULL;
        while (true) {
            //查询并更新游标
            $ret = $this->handle->zScan($this->name, $iterator, $pattern, 10);

            //没有更多数据了
            if (false === $ret) {
                break;
            }

            //逐个返回
            foreach ($ret as $key => $val) {
                yield [$key => $val];
            }
        }
    }

    /**
     * 返回所有数据
     * @return array
     */
    public function all(): array
    {
        return $this->selectByRank();
    }
}