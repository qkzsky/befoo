<?php

class AdminController extends ApplicationController
{

    protected $menu_list   = array();
    protected $session_key = 'admin_info';
    protected $layout      = 'admin';

    public function init()
    {
        parent::init();

        // 命令行执行不判断权限
        if (!$this->getRequest()->isCli())
        {
            $user_info = $this->getUserInfo();
            $user_id   = isset($user_info['user_id']) ? $user_info['user_id'] : null;

            // 判断是否有权限
            $AdminModel = AdminModel::getInstance();
            if (!$resource_info = $AdminModel->checkAuth($this->getResourceKey(), $user_id))
            {
                // 未登录则跳转至登陆页
                if ($user_id === null)
                {
                    $this->redirect('/admin/user/login');
                }
                else
                {
                    $this->redirect('/error/accessDenied');
                }
                exit;
            }
            $this->getView()->assign('_curr_resource_', $resource_info);
        }

        // cli, ajax 请求不加载layout
        if ($this->getRequest()->isCli() || $this->getRequest()->isXmlHttpRequest())
        {
            $this->getView()->setLayout(null);
        }
        else
        {
            // 获取菜单信息
            if (!empty($user_info))
            {
                $UserModel       = new UserModel();
                $this->menu_list = $UserModel->getMenuList($user_info['user_id']);
                $this->getView()->assign('menu_list', $this->menu_list);
            }

            // 初始化Layout目录
            $this->getView()->setLayoutPath(
                $this->getConfig()->application->directory . "/modules/" . $this->getModuleName() . "/views/layouts"
            );
        }
    }

    /**
     * 获取资源KEY
     * @return string
     */
    private function getResourceKey()
    {
        $request = $this->getRequest();
        return $request->getModuleName() . '-' . $request->getControllerName() . '-' . $request->getActionName();
    }

    /**
     * 设置用户Session
     * @param array $user_info
     * @return type
     */
    public function setUserInfo(array $user_info)
    {
        return $this->session ? $this->session->set($this->session_key, $user_info) : false;
    }

    /**
     * 获取用户Session
     * @return type
     */
    public function getUserInfo()
    {
        return $this->session ? $this->session->get($this->session_key) : null;
    }

    /**
     * 删除用户Session
     * @return type
     */
    public function clearUserInfo()
    {
        return $this->session ? $this->session->del($this->session_key) : false;
    }

    /**
     * 格式化排序选项
     * @param string $string 待格式化的字符串
     * @param array $fields 参与排序的字段
     * @return array array($field => $sort, ...)
     */
    public function formatSort($string, array $fields)
    {
        $sort_option = array();
        if (strpos($string, ',') > 0)
        {
            $params = explode(',', $string);
            foreach ($params as $key => $value)
            {
                if ($key % 2 === 0 && in_array($value, $fields))
                {
                    if (isset($params[$key + 1]) && in_array(strtolower($params[$key + 1]), array("asc", "desc")))
                    {
                        $sort_option[$value] = $params[$key + 1];
                    }
                }
            }
        }
        return $sort_option;
    }

}
