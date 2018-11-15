对Redis进行封装
=

* 配置

    本模块使用redis配置数组,其中应包含hostname,port,以及可选的password
    
* 任意类型值的存储与取出

    $obj->redis(string $key, $value = null, bool $replace = true, int $expire = 0): RedisJson
    
    以上将以JSON格式对value值进行存储, 并可通过以下方法读取
    
    redis(string $key) 
    
    或
    
    $obj->get()
    
* 字符串类型的存储与取出
    
    $string=redisString(string $key, ?string $value = null, bool $replace = true, int $expire = 0): RedisString
    
    可通过以下方法读取 
    
    redisString(string $key)
    
    或
    
    $string->get()
    
* 整数类型的存储与取出

    redisInt(string $key, ?int $value = null, bool $replace = true, int $expire = 0): RedisInt
    
* 浮点类型的存储与取出

    redisFloat(string $key, ?float $value = null, bool $replace = true, int $expire = 0): RedisFloat
    
* 位 类型的存储与取出

    redisBit(string $key, ?int $value = null, bool $replace = true, int $expire = 0): RedisBit
    
* Hash(键值对)的存储与取出

    redisHash(string $key, ?array $fields = null): RedisHash
    
* List(有序数组)的存储与取出

    redisList(string $key, ?array $values = null): RedisList
    
* Set(集合)的存储与取出

    redisSet(string $key, $members = null): RedisSet
    
* SortedSet(有序集合)的存储与取出

    redisSortedSet(string $key, array $members = null): RedisSortedSet
    
* 删除一个存储键或多个

    redisDelete(...$keys): int
    
* 判断是否存在指定的键
    
    redisExists(string $key): bool
    
            
                       
    
    
    
        

