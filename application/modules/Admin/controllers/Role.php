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
 * @version $Id: Index.php, v 1.0 2015-6-21 14:49:53
 */

use Respect\Validation\Validator as v;
use Respect\Validation\Exceptions\ValidationException;

/**
 * Description of Index
 *
 * @author kuangzhiqiang
 */
class RoleController extends AdminController
{

    /**
     * 角色管理
     */
    public function manageAction()
    {
        $this->display("manage");
    }

    /**
     * 获取分页数据
     */
    public function getPageDataAction()
    {
        $page_index  = $this->getRequest()->getQuery('page_index', 1);
        $page_size   = $this->getRequest()->getQuery('page_size', 20);
        $sort        = $this->getRequest()->getQuery('sort', '');
        $sort_option = $this->formatSort($sort, array('role_id'));

        $search_params = array(
            'role_name' => $this->getRequest()->getQuery('role_name', ''),
            'role_key'  => $this->getRequest()->getQuery('role_key', ''),
            'enable'    => $this->getRequest()->getQuery('enable', ''),
        );

        $RoleModel  = RoleModel::getInstance();
        $user_count = $RoleModel->getPageCount($search_params);
        $user_list  = $user_count ? $RoleModel->getPageList($search_params, $page_size, $page_index, $sort_option) : array();
        $this->getResponse()->setBody(json_encode(array(
            'total' => $user_count,
            'data'  => $user_list,
        )));
    }

    /**
     * 检查角色Key是否唯一
     * @param string $role_key
     * @return bool
     */
    public function uniqueRoleKey($role_key)
    {
        $condition = 'role_key = :role_key';
        $bind      = [':role_key' => $role_key];

        $role_id = $this->getRequest()->getQuery('id');
        if (!empty($role_id))
        {
            $condition .= ' and role_id != :role_id';
            $bind += [':role_id' => $role_id];
        }
        $RoleModel = RoleModel::getInstance();
        return $RoleModel->getCount($condition, $bind) ? false : true;
    }

    /**
     * 添加资源
     */
    public function addRoleAction()
    {
        if ($this->getRequest()->isPost())
        {
            $post_data = $this->getRequest()->getPost();
            try
            {
                v::keySet(
                    v::key("role_name", v::stringType()->length(null, 30)),
                    v::key("role_key", v::allOf(
                        v::stringType()->length(null, 30),
                        v::callback(array($this, 'uniqueRoleKey'))->setName('role_key unique')
                    )),
                    v::key("enable", v::in(array(YES, NO))),
                    v::key("remark", v::stringType()->length(null, 200))
                )->check($post_data);
            }
            catch (ValidationException $e)
            {
                $this->responseJson(1001, $e->getMainMessage());
                return;
            }

            // 插入
            $RoleModel = RoleModel::getInstance();
            $params    = array(
                'role_name'   => $post_data['role_name'],
                'role_key'    => $post_data['role_key'],
                'enable'      => $post_data['enable'],
                'remark'      => $post_data['remark'],
                'create_time' => \Db\DbString::prepare("now()"),
            );
            $RoleModel->insert($params);
            $this->responseJson(0, 'success');
        }
        else
        {
            $assign = array(
                'request_uri' => $this->getRequest()->getRequestUri(),
            );
            $this->display('addRole', $assign);
        }
    }

    public function editRoleAction()
    {
        $role_id   = $this->getRequest()->getQuery('id');
        $RoleModel = RoleModel::getInstance();
        $role_info = $RoleModel->getInfo('role_id', $role_id, '*');
        if (empty($role_info))
        {
            echo "角色ID[{$role_id}]不存在";
            return;
        }

        if ($this->getRequest()->isPost())
        {
            $post_data = $this->getRequest()->getPost();
            try
            {
                v::keySet(
                    v::key("role_name", v::stringType()->length(null, 30)),
                    v::key("role_key", v::allOf(
                        v::stringType()->length(null, 30),
                        v::callback(array($this, 'uniqueRoleKey'))->setName('role_key unique')
                    )),
                    v::key("enable", v::in(array(YES, NO))),
                    v::key("remark", v::stringType()->length(null, 200))
                )->check($post_data);
            }
            catch (ValidationException $e)
            {
                $this->responseJson(1001, $e->getMainMessage());
                return;
            }

            // 插入
            $RoleModel = RoleModel::getInstance();
            $params    = array(
                'role_name'   => $post_data['role_name'],
                'role_key'    => $post_data['role_key'],
                'enable'      => $post_data['enable'],
                'remark'      => $post_data['remark'],
                'create_time' => \Db\DbString::prepare("now()"),
            );
            $RoleModel->update($params, 'role_id = :role_id', [':role_id' => $role_id]);
            $this->responseJson(0, 'success');
        }
        else
        {
            $assign = array(
                'role_info'   => $role_info,
                'request_uri' => $this->getRequest()->getRequestUri() . '?id=' . $role_id,
            );
            $this->display('editRole', $assign);
        }
    }

    /**
     * 检查资源ID是否存在
     * @param array $resource_id
     * @return bool
     */
    public function existResourceID($resource_id)
    {
        if (!empty($resource_id))
        {
            $ResourceModel = ResourceModel::getInstance();
            list($condition, $bind) = Helper\Sql::buildBindCondition('resource_id', $resource_id, 'in');
            return count($resource_id) === (int)$ResourceModel->getCount($condition, $bind);
        }
        return true;
    }

    /**
     * 给角色分配资源
     */
    public function allocResourceAction()
    {
        $role_id   = $this->getRequest()->getQuery('id');
        $RoleModel = RoleModel::getInstance();
        $role_info = $RoleModel->getInfo('role_id', $role_id, '*');
        if (empty($role_info))
        {
            echo "角色ID[{$role_id}]不存在";
            return;
        }

        if ($this->getRequest()->isPost())
        {
            $post_data = $this->getRequest()->getPost();
            try
            {
                v::keySet(
                    v::key("resource_id", v::callback(array($this, 'existResourceID')), false)
                )->check($post_data);
            }
            catch (ValidationException $e)
            {
                $this->responseJson(1001, $e->getMainMessage());
                return;
            }

            // 分配角色权限
            $RoleModel = RoleModel::getInstance();
            $RoleModel->allocResource($role_id, $this->getRequest()->getPost('resource_id', array()));
            $this->responseJson(0, 'success');
        }
        else
        {
            $assign = array(
                'resource_tree_nodes' => $RoleModel->getResourceZTreeNodes($role_id),
                'request_uri'         => $this->getRequest()->getRequestUri() . '?id=' . $role_id,
            );
            $this->display('allocResource', $assign);
        }
    }

}
