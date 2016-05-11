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
class AdminModel extends ApplicationModel
{

    public function __construct()
    {
        $this->db = Db\Mysql::getInstance('befoo');
    }

    /**
     * 判断是否拥有权限
     * @param string $resource_key
     * @param int $uid
     * @return array
     */
    public function checkAuth($resource_key, $uid = 0)
    {
        // 判断权限是否为有效资源
        $sql           = "SELECT resource_id, resource_name, resource_key, public, login FROM `resource` WHERE `resource_key` = :resource_key AND `enable` = :enable";
        $resource_info = $this->db->fetchRow($sql, array(':resource_key' => $resource_key, ':enable' => YES));

        if (!empty($resource_info))
        {
            // 开放资源，需判断是否是需要登录
            if ($resource_info['public'] === YES)
            {
                if ($resource_info['login'] === NO || !empty($uid))
                {
                    return $resource_info;
                }
            }

            if (!empty($uid))
            {
                // 检查用户是否正常
                $sql       = "SELECT role_id, admin FROM `user` WHERE `user_id` = :uid AND `enable` = :enable";
                $user_info = $this->db->fetchRow($sql, array(':uid' => $uid, ':enable' => YES));
                if (!empty($user_info))
                {
                    // 超级管理员不受权限控制
                    if ($user_info['admin'] === YES)
                    {
                        return $resource_info;
                    }

                    // 判断用户角色是否拥有权限
                    $sql = "SELECT count(1) FROM `role` WHERE `role_id` = :role_id AND `enable` = :enable ";
                    if ($this->db->fetchOne($sql, array(':role_id' => $user_info['role_id'], ':enable' => YES)))
                    {
                        $sql = "SELECT count(1) FROM `role_resource_map` WHERE `role_id` = :role_id AND `resource_id` = :resource_id ";
                        if ($this->db->fetchOne($sql, array(':role_id' => $user_info['role_id'], ':resource_id' => $resource_info['resource_id'])))
                        {
                            return $resource_info;
                        }
                    }
                }
            }
        }

        return array();
    }

}
