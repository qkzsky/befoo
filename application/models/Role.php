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
class RoleModel extends ApplicationModel
{

    public function __construct()
    {
        $this->db    = Db\Mysql::getInstance('befoo');
        $this->table = 'role';
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
            $sql       = "SELECT {%fields} FROM `role`";
            $condition = " WHERE 1";
            $bind_data = array();

            if (!empty($params['role_name']))
            {
                $condition .= " AND role_name LIKE :role_name";
                $bind_data += array(':role_name' => "%{$params['role_name']}%");
            }
            if (!empty($params['role_key']))
            {
                $condition .= " AND role_key LIKE :role_key";
                $bind_data += array(':role_key' => "%{$params['role_key']}%");
            }
            if (!empty($params['enable']))
            {
                $condition .= " AND enable = :enable";
                $bind_data += array(':enable' => "{$params['enable']}");
            }

            $sql .= $condition;
            $_info[$key] = array($sql, $bind_data);
        }

        return $_info[$key];
    }

    /**
     * 获取用户总数
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
     * 获取用户列表
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
        $sql .= Helper\Sql::buildSort($sort_options, 'role_id');
        $sql .= Helper\Sql::buildLimit($page_size, $page_index);

        return $this->db->fetchAll($sql, $bind_data);
    }

    /**
     * 获取角色的资源分配列表
     * @param $role_id
     * @return array
     */
    public function getResourceList($role_id)
    {
        $sql        = "SELECT rm.`resource_id` FROM role r JOIN role_resource_map rm ON (r.`role_id` = rm.`role_id`) WHERE r.`role_id` = :role_id";
        $parameters = [':role_id' => $role_id];
        return $this->db->fetchAll($sql, $parameters);
    }

    /**
     * 获取资源zTree格式数组
     * @param $role_id
     * @return array
     */
    public function getResourceZTreeNodes($role_id)
    {
        $sql   = "SELECT rs.`resource_id` AS id, resource_pid AS pId, resource_name AS `name`, IF(rrm.`role_id`, true, false) AS checked"
            . " FROM resource rs"
            . " LEFT JOIN role_resource_map rrm ON (rs.`resource_id` = rrm.`resource_id` AND rrm.role_id = :role_id)"
            . " where rs.enable = :enable and rs.public = :public and login = :login"
            . " order by rs.menu_sort";
        $nodes = $this->db->fetchAll($sql, [
            ':role_id' => $role_id,
            ':enable'  => YES,
            ':public'  => NO,
            ':login'   => YES,
        ]);
        foreach ($nodes as &$item)
        {
            $item['checked'] = !empty($item['checked']);
        }
        unset($item);

        return $nodes;
    }

    /**
     * 分配角色资源
     * @param int $role_id
     * @param array $resource_id
     */
    public function allocResource($role_id, array $resource_id = array())
    {
        $this->begin();
        // 清空之前的数据
        $this->db->delete('role_resource_map', 'role_id = :role_id', [
            ':role_id' => $role_id
        ]);

        // 插入新的数据
        if (!empty($resource_id))
        {
            $insert_data = array();
            foreach ($resource_id as $id)
            {
                $insert_data[] = array(
                    'role_id'     => $role_id,
                    'resource_id' => $id,
                );
            }
            $this->db->insertMulti('role_resource_map', $insert_data);
        }

        $this->commit();
    }
}
