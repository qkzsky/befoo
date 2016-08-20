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
class UserModel extends ApplicationModel
{

    public function __construct()
    {
        $this->db    = Db\Mysql::getInstance('befoo');
        $this->table = 'user';
    }

    /**
     * 获取用户登录信息
     * @param string $username
     * @param string $password
     * @return array
     */
    public function getLoginInfo($username, $password)
    {
        $sql = "SELECT * FROM `user` WHERE `username` = :username AND `password` = :password AND `enable` = :enable";
        return $this->db->fetchRow($sql, array(
            ':username' => $username,
            ':password' => $this->encryptPassword($username, $password),
            ':enable'   => YES
        ));
    }

    /**
     * 加密密码
     * @param string $username
     * @param string $password
     * @return string
     */
    public function encryptPassword($username, $password)
    {
        $sha_str      = sha1($password);
        $sha_len      = mb_strlen($sha_str, 'utf-8');
        $username_len = mb_strlen($username, 'utf-8');
        $encrypt_str  = '';
        for ($i = 0; $i < $sha_len; $i++)
        {
            $encrypt_str .= $sha_str[$i] ^ $username[$i % $username_len];
        }

        return md5(sha1($encrypt_str));
    }

    /**
     * 获取用户菜单列表
     * @param string $user_id
     * @return array
     */
    public function getMenuList($user_id)
    {
        $admin = $this->getOne('admin', 'user_id = :user_id', [':user_id' => $user_id]);
        if ($admin === YES)
        {
            $ResourceModel = ResourceModel::getInstance();
            $list          = $ResourceModel->getAll('*', 'enable = :enable AND resource_type = :resource_type', [
                ':enable'        => YES,
                ':resource_type' => ResourceModel::TYPE_MENU,
            ]);
        }
        else
        {
            $sql  = "SELECT rs.* FROM `user` u"
                . "  JOIN role r ON (u.`role_id` = r.`role_id`)"
                . "  JOIN role_resource_map rrm ON (r.`role_id`=rrm.`role_id`)"
                . "  JOIN resource rs ON (rrm.`resource_id` = rs.`resource_id`)"
                . " WHERE u.`user_id` = :user_id"
                . "  AND r.enable = :r_enable"
                . "  AND rs.enable = :rs_enable"
                . "  AND rs.resource_type = :resource_type"
                . " ORDER BY rs.resource_pid ASC, rs . menu_sort ASC";
            $list = $this->db->fetchAll($sql, array(
                ':user_id'       => $user_id,
                ':r_enable'      => YES,
                ':rs_enable'     => YES,
                ':resource_type' => ResourceModel::TYPE_MENU,
            ));
        }

        $menu_list = array();
        foreach ($list as $row)
        {
            if ((int) $row['resource_pid'] === 0)
            {
                $menu_list[$row['resource_id']] = $row;
            }
            else
            {
                $menu_list[$row['resource_pid']]['menu_list'][$row['resource_id']] = $row;
            }
        }

        return $menu_list;
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
            $sql       = "SELECT {%fields} FROM `user` u join role r on(u.role_id = r.role_id)";
            $condition = " WHERE 1";
            $bind_data = array();

            if (!empty($params['username']))
            {
                $condition .= " AND username LIKE :username";
                $bind_data += array(':username' => "%{$params['username']}%");
            }
            if (!empty($params['nickname']))
            {
                $condition .= " AND nickname LIKE :nickname";
                $bind_data += array(':nickname' => "%{$params['nickname']}%");
            }
            if (!empty($params['role_id']))
            {
                $condition .= " AND role_id = :role_id";
                $bind_data += array(':role_id' => $params['role_id']);
            }
            if (!empty($params['enable']))
            {
                $condition .= " AND u.enable = :enable";
                $bind_data += array(':enable' => $params['enable']);
            }
            if (!empty($params['admin']))
            {
                $condition .= " AND admin = :admin";
                $bind_data += array(':admin' => $params['admin']);
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
        $sql = str_replace('{%fields}', 'u.user_id, u.username, u.nickname, u.enable, u.admin, u.login_times, u.last_login_time, u.last_login_ip, u.create_time, r.role_name', $sql);
        $sql .= Helper\Sql::buildSort($sort_options, 'user_id');
        $sql .= Helper\Sql::buildLimit($page_size, $page_index);

        return $this->db->fetchAll($sql, $bind_data);
    }
}
