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
     * - 'limit' => array(0, 1),
     * - 'get' => 'some_other_pattern_*' or an array of patterns,
     * - 'sort' => 'asc' or 'desc',
     * - 'alpha' => TRUE,
     * - 'store' => 'external-key'
     * @return array
     */
    public function sort(array $options):array
    {
        /**
         * @var $handle \redis
         */
        $handle=$this->handle;
        return $handle->sort($this->key, $options);
    }
}
