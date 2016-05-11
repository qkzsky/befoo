<?php

/*
 * Mi Framework
 *
 * Copyright (C) 2015 by kuangzhiqiang. All rights reserved
 *
 * To contact the author write to {@link mailto:kuangzhiqiang@xiaomi.com}
 *
 * @author kuangzhiqiang
 * @encoding UTF-8
 * @version $Id: Demo.php, v 1.0 2015-3-7 20:16:30
 */

/**
 * Description of Demo
 *
 * @author kuangzhiqiang
 */
class ResourceModel extends ApplicationModel
{
    const TYPE_MENU   = 'menu';
    const TYPE_ACTION = 'action';

    const TYPE_LIST = array(
        self::TYPE_MENU,
        self::TYPE_ACTION
    );

    public function __construct()
    {
        $this->db    = Db\Mysql::getInstance('befoo');
        $this->table = 'resource';
    }

    /**
     * 构建列表sql语句
     * @param array $params
     * @return array array($sql, $bind)
     */
    private function buildPageSql(array $params)
    {
        static $_info = array();
        $key = to_guid_string($params);
        if (!isset($_info[$key]))
        {
            $sql       = "SELECT {%fields} FROM resource";
            $condition = " WHERE 1";
            $bind_data = array();

            if (!empty($params['resource_name']))
            {
                $condition .= " AND resource_name LIKE :resource_name";
                $bind_data += array(':resource_name' => "%{$params['resource_name']}%");
            }
            if (!empty($params['resource_type']))
            {
                $condition .= " AND resource_type = :resource_type";
                $bind_data += array(':resource_type' => "{$params['resource_type']}");
            }
            if (!empty($params['resource_key']))
            {
                $condition .= " AND resource_key LIKE :resource_key";
                $bind_data += array(':resource_key' => "%{$params['resource_key']}%");
            }

            $sql .= $condition;
            $_info[$key] = array($sql, $bind_data);
        }

        return $_info[$key];
    }

    /**
     * 获取资源总数
     * @param array $params
     * @return int
     */
    public function getPageCount($params)
    {
        list($sql, $bind_data) = $this->buildPageSql($params);
        $sql = str_replace('{%fields}', 'count(1)', $sql);
        return $this->db->fetchOne($sql, $bind_data);
    }

    /**
     * 获取资源列表
     * @param array $params
     * @param int $page_size
     * @param int $page_index
     * @param array $sort_options
     * @return array
     */
    public function getPageList(array $params, $page_size = 0, $page_index = 0, $sort_options = array())
    {
        list($sql, $bind_data) = $this->buildPageSql($params);
        $sql = str_replace('{%fields}', '*', $sql);
        $sql .= Helper\Sql::buildSort($sort_options, 'resource_id');
        $sql .= Helper\Sql::buildLimit($page_size, $page_index);

        return $this->db->fetchAll($sql, $bind_data);
    }

}
