<?php
declare(strict_types=1);
namespace icePHP;

/**
 * 连接服务器
 */
final class RedisConnection
{
    /**
     * @var RedisConnection
     */
    private static $instance;

    /**
     * @var \redis
     */
    private $handle;

    /**
     * 禁止直接实例化
     */
    private function __construct()
    {
    }

    /**
     * 获取连接对象单例
     * @return RedisConnection
     */
    public static function instance()
    {
        if (!self::$instance) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    /**
     * 创建连接句柄
     * @return Redis 实际Redis连接句柄
     * @throws \Exception
     */
    public function connect(): \redis
    {
        //如果已经连接,返回连接句柄
        if ($this->handle) {
            return $this->handle;
        }

        // 记录开始时间
        $start = timeLog();

        // 取此集合的配置
        $config = $config = Config::get('redis');

        //尝试连接,出错就抛异常吧
        $this->handle = new \redis();
        $this->handle->connect($config['hostname'], $config['port']);

        //如果设置了密码,要通过AUTH
        if (isset($config['password']) and $config['password']) {
            //如果密码不正确,提示
            if (!$this->auth($config['password'])) {
                throw new \Exception('redis password not match.');
            }
        }

        // 记录调试信息
        Debug::set("connect to redis {$config['hostname']} ,persist:" . timeLog($start) . 'ms');
        return $this->handle;
    }

    /**
     * 如果开启了密码保护的话，在每次连接 Redis 服务器之后，就要使用 AUTH 命令解锁，解锁之后才能使用其他 Redis 命令。
     * @param $password
     * @return bool
     */
    private function auth($password)
    {
        return $this->handle->auth($password);
    }

    /**
     * 返回关于 Redis 服务器的各种信息和统计值。
     * @return string
     */
    public function info()
    {
        return $this->handle->info();
    }

    /**
     * 客户端向服务器发送一个 PING ，然后服务器返回客户端一个 PONG 。
     * @internal
     * @return string 'PONG'
     */
    public function ping()
    {
        return $this->handle->ping() === 'PONG';
    }

    /**
     * 打印一个特定的信息 message ，测试时使用。
     * @internal
     * @return mixed
     */
    public function echoMessage(string $msg)
    {
        return $this->handle->echo($msg);
    }
}