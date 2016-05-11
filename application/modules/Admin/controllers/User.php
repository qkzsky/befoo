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
class UserController extends AdminController
{

    /**
     * 登录
     * @return boolean
     */
    public function loginAction()
    {
        // 已登录跳转至首页
        $user_info = $this->getUserInfo();
        if (!empty($user_info))
        {
            $this->redirect('/admin/index');
            return false;
        }

        if ($this->getRequest()->isPost())
        {
            $request = $this->getRequest();

            $geetest_challenge = $request->getPost('geetest_challenge');
            $geetest_validate  = $request->getPost('geetest_validate');
            $geetest_seccode   = $request->getPost('geetest_seccode');

            // 校验验证码
            $geetest_conf = $this->getConfig()->geetest;
            $GtSdk        = new GeetestLib($geetest_conf->captcha_id, $geetest_conf->private_key);
            if (!$GtSdk->validate($this->session->get("geetest_server"), $geetest_challenge, $geetest_validate, $geetest_seccode))
            {
                $this->responseJson(ErrorCode::INVALID_CAPTCHA, '验证码验证失败');
                return false;
            }

            $username = $request->getPost('username');
            $password = $request->getPost('password');
            if ($username === '' || $password === '')
            {
                $this->responseJson(ErrorCode::INVALID_PARAMETER, '用户名或密码不可为空');
                return false;
            }

            $UserModel = UserModel::getInstance();
            $user_info = $UserModel->getLoginInfo($username, $password);
            if (empty($user_info))
            {
                $this->responseJson(ErrorCode::USER_NOTFOUND, '用户名或密码错误');
                return false;
            }

            // 更新用户登录信息
            $login_params = array(
                'login_times'     => \Db\DbString::prepare('login_times+1'),
                'last_login_time' => \Db\DbString::prepare('now()'),
                'last_login_ip'   => get_client_ip(),
            );
            $UserModel->update($login_params, "user_id = :user_id", array(
                ':user_id' => $user_info['user_id']
            ));

            // 登录成功
            $this->setUserInfo($user_info);
            $this->responseJson(ErrorCode::SUCCESS, 'success');
            return false;
        }

        $assign = array(
            "title" => "Admin Login"
        );
        $this->getView()->setLayout(null);
        $this->display("login", $assign);
    }

    /**
     * 登出
     */
    public function logoutAction()
    {
        $this->clearUserInfo();
        $this->redirect('/admin/user/login');
    }

    /**
     * 用户管理
     */
    public function manageAction()
    {
        $role_list = RoleModel::getInstance();
        $assign    = array(
            'role_list' => $role_list->getAll(array('role_id', 'role_name'))
        );
        $this->display("manage", $assign);
    }

    /**
     * 获取分页数据
     */
    public function getPageDataAction()
    {
        $page_index  = $this->getRequest()->getQuery('pageIndex', 1);
        $page_size   = $this->getRequest()->getQuery('pageSize', 20);
        $sort        = $this->getRequest()->getQuery('sort', '');
        $sort_option = $this->formatSort($sort, array('user_id', 'role_id', 'enable', 'admin'));

        $search_params = array(
            'username' => $this->getRequest()->getQuery('username', ''),
            'nickname' => $this->getRequest()->getQuery('nickname', ''),
            'role_id'  => $this->getRequest()->getQuery('role_id', ''),
            'enable'   => $this->getRequest()->getQuery('enable', ''),
            'admin'    => $this->getRequest()->getQuery('admin', ''),
        );

        $UserModel  = UserModel::getInstance();
        $user_count = $UserModel->getPageCount($search_params);
        $user_list  = $user_count ? $UserModel->getPageList($search_params, $page_size, $page_index, $sort_option) : array();
        $this->getResponse()->setBody(json_encode(array(
            'total' => $user_count,
            'data'  => $user_list,
        )));
    }

    /**
     * 检查角色ID是否存在
     * @param string $role_id
     * @return bool
     */
    public function existRoleID($role_id)
    {
        if (!empty($role_id))
        {
            $RoleModel = RoleModel::getInstance();
            return $RoleModel->getCount('role_id = :role_id', array(
                ':role_id' => $role_id
            )) ? true : false;
        }
        return true;
    }

    /**
     * 检查用户名是否唯一
     * @param $username
     * @return bool
     */
    public function uniqueUsername($username)
    {
        $condition = 'username = :username';
        $bind      = [':username' => $username];

        $user_id = $this->getRequest()->getQuery('id');
        if (!empty($user_id))
        {
            $condition .= ' and user_id != :user_id';
            $bind += [':user_id' => $user_id];
        }
        $UserModel = UserModel::getInstance();
        return $UserModel->getCount($condition, $bind) ? false : true;
    }

    public function addUserAction()
    {
        if ($this->getRequest()->isPost())
        {
            $post_data = $this->getRequest()->getPost();
            try
            {
                v::keySet(
                    v::key("username", v::allOf(
                        v::stringType()->length(null, 30),
                        v::callback([$this, 'uniqueUsername'])
                    )),
                    v::key("password", v::stringType()->length(null, 30)),
                    v::key("confirm_password", v::Equals($this->getRequest()->getPost('password'))),
                    v::key("role_id", v::callback([$this, 'existRoleID'])),
                    v::key("nickname", v::stringType()->length(null, 30), false),
                    v::key("enable", v::in(array(YES, NO))),
                    v::key("admin", v::in(array(YES, NO)))
                )->check($post_data);
            }
            catch (ValidationException $e)
            {
                $this->responseJson(1001, $e->getMainMessage());
                return;
            }

            // 插入
            $UserModel = UserModel::getInstance();
            $params    = array(
                'username'    => $post_data['username'],
                'password'    => $UserModel->encryptPassword($post_data['username'], $post_data['password']),
                'role_id'     => $post_data['role_id'],
                'nickname'    => $post_data['nickname'],
                'enable'      => $post_data['enable'],
                'admin'       => $post_data['admin'],
                'create_time' => \Db\DbString::prepare("now()"),
            );
            $UserModel->insert($params);
            $this->responseJson(0, 'success');
        }
        else
        {
            $RoleModel = RoleModel::getInstance();
            $assign    = array(
                'role_list'   => $RoleModel->getAll(array('role_id', 'role_name', 'enable')),
                'request_uri' => $this->getRequest()->getRequestUri(),
            );
            $this->display('addUser', $assign);
        }
    }

    public function editUserAction()
    {
        $user_id   = $this->getRequest()->getQuery('id');
        $UserModel = UserModel::getInstance();
        $user_info = $UserModel->getInfo('user_id', $user_id, '*');
        if (empty($user_info))
        {
            echo "用户ID[{$user_id}]不存在";
            return;
        }

        if ($this->getRequest()->isPost())
        {
            $post_data = $this->getRequest()->getPost();
            try
            {
                v::keySet(
                    v::key("username", v::allOf(
                        v::stringType()->length(null, 30),
                        v::callback([$this, 'uniqueUsername'])
                    )),
                    v::key("role_id", v::callback([$this, 'existRoleID'])),
                    v::key("nickname", v::stringType()->length(null, 30), false),
                    v::key("enable", v::in(array(YES, NO))),
                    v::key("admin", v::in(array(YES, NO)))
                )->check($post_data);
            }
            catch (ValidationException $e)
            {
                $this->responseJson(1001, $e->getMainMessage());
                return;
            }

            // 插入
            $UserModel = UserModel::getInstance();
            $params    = array(
                'username' => $post_data['username'],
                'role_id'  => $post_data['role_id'],
                'nickname' => $post_data['nickname'],
                'enable'   => $post_data['enable'],
                'admin'    => $post_data['admin'],
            );
            $UserModel->update($params, 'user_id = :user_id', [':user_id' => $user_id]);
            $this->responseJson(0, 'success');
        }
        else
        {
            $RoleModel = RoleModel::getInstance();
            $assign    = array(
                'user_info'   => $user_info,
                'role_list'   => $RoleModel->getAll(array('role_id', 'role_name', 'enable')),
                'request_uri' => $this->getRequest()->getRequestUri() . '?id=' . $user_id,
            );
            $this->display('editUser', $assign);
        }
    }
}
