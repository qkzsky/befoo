<?php

namespace Db;

/**
 * Redis管理器
 *
 * @author kuangzhiqiang
 *
 */
class Redis
{

    private static $_connects;

    /**
     * 取得Redis实例
     * @param string $id
     * @return \Redis
     * @throws \Exception
     */
    static public function getInstance($id = 'default')
    {
        if (!isset(self::$_connects[$id]))
        {
            $config = \Yaf_Application::app()->getConfig()->db->redis;
            if (!isset($config->$id))
            {
                throw new \Exception("not found redis_config for {$id}", E_ERROR);
            }
            $db_config = $config->$id;

            // 连接redis
            $redis = new \Redis();
            $redis->connect($db_config->host, $db_config->port);
            if (isset($db_config->auth))
            {
                $redis->auth($db_config->auth);
            }
            self::$_connects[$id] = $redis;
        }

        return self::$_connects[$id];
    }

}
