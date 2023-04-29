<?php

namespace App\Libraries;

use Illuminate\Redis\Connections\Connection;

class Redis
{
    private Connection $redis;

    public function __construct()
    {
        $this->redis = \Illuminate\Support\Facades\Redis::connection();
    }

    public function set($key, $data)
    {
        return $this->redis->set($key, $data);
    }

    public function get($key)
    {
        return $this->redis->get($key);
    }

    public function putForFiveMinutes($key, $data)
    {
        return $this->redis->setex($key, 60 * 5, $data);
    }

    public function delete($key)
    {
        return $this->redis->del($key);
    }
}
