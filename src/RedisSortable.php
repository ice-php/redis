<?php
declare(strict_types=1);
namespace icePHP;

/**
 * 可排序的Redis对象Trait;包括Set,SortedSet,List
 */
trait RedisSortable
{
    /**
     * 获取排序结果
     * @param array $options 排序参数
     * - 'by' => 'some_pattern_*',
     * - 'limit' => array(0, 1), 分页
     * - 'get' => 'some_other_pattern_*' or an array of patterns,
     * - 'sort' => 'asc' or 'desc', 升序/降序
     * - 'alpha' => TRUE, 排序默认以数字作为对象，值被解释为双精度浮点数，然后进行比较。  如果指定此项,则按字符串进行排序
     * - 'store' => 'external-key' 结果输出到哪个集合中
     * @return array
     */
    public function sort(array $options):array
    {
        /**
         * @var $handle \redis
         */
        $handle=$this->handle;
        return $handle->sort($this->name, $options);
    }
}
