<?php
/**
 * GEO存储与检索
 * User: 蓝冰大侠
 * Date: 2018/11/30
 * Time: 13:25
 */

namespace icePHP;


class RedisGeo extends RedisElement
{
    /**
     * 获取当前存储对象的类型(字符串格式)
     * @return string
     */
    public function getType(): string
    {
        return 'GEO';
    }

    /**
     * 存储一个坐标及相关名称
     * @param $name string
     * @param $lng float
     * @param $lat float
     * @return int 新添加的空间元素数量， 不包括那些已经存在但是被更新的元素。
     */
    public function insert(string  $name,float $lng, float $lat): int
    {
        return intval($this->handle->rawCommand('geoadd', [$this->name, $lng, $lat, $name]));
    }

    /**
     * 存储多个坐标及相关名称
     * @param array $values [$name=>[lng(经),lat(纬)],...]
     * @return int 新添加的空间元素数量， 不包括那些已经存在但是被更新的元素。
     */
    public function inserts(array $values): int
    {
        $params = [$this->name];
        foreach ($values as $name=>$pos) {
            $params[] = $pos[0];
            $params[] = $pos[1];
            $params[] = $name;
        }
        return intval($this->handle->rawCommand('geoadd', $params));
    }

    /**
     * 根据地点名称,获取坐标
     * @param $name string|array
     * @return array [[lng,lat],...]
     */
    public function getPos($name): array
    {
        $params = [$this->name];
        if (!is_array($name)) {
            $params[] = $name;
        } else {
            $params = array_merge($params, $name);
        }
        $ret = $this->handle->rawCommand('geopos', $params);

        //可能需要转换格式
        return $ret;
    }

    /**
     * 计算两个地点之间的距离(米)
     * @param string $name1 地点名称
     * @param string $name2
     * @return float 米
     */
    public function getDistance(string $name1, string $name2): float
    {
        return $this->handle->rawCommand('geodist', [$this->name, $name1, $name2]);
    }

    /**
     * 根据坐标查询附近的地点
     * @param float $lng 指定点的经度
     * @param float $lat 指定点的纬度
     * @param float $radius 半径(米)
     * @param int $limit 获取地点的个数
     * @param string $order 排序 ,默认近的优先
     * @return array
     */
    public function near(float $lng, float $lat, float $radius, int $limit = 10, string $order = 'asc'): array
    {
        $ret = $this->handle->rawCommand('georadius', [$lng, $lat, $radius, 'm', 'withcoord', 'withdist', $order, 'count', $limit]);

        //可能需要转换格式
        return $ret;
    }

    /**
     * 根据地点名称查询附近的地点
     * @param string $name 指定地点的名称
     * @param $radius float 半径(米)
     * @param int $limit 数量
     * @param string $order 排序
     * @return array
     */
    public function nearByName(string $name, $radius, int $limit = 10, string $order = 'asc'): array
    {
        $ret = $this->handle->rawCommand('getradiusbymember', [$name, $radius, 'm', 'withcoord', 'withdist', $order, 'count', $limit]);

        //可能需要转换格式
        return $ret;
    }
}