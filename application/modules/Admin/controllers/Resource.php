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
 * @version $Id: Index.php, v 1.0 2015-3-6 14:49:53
 */

use Respect\Validation\Validator as v;
use Respect\Validation\Exceptions\ValidationException;

/**
 * Description of Index
 *
 * @author kuangzhiqiang
 */
class ResourceController extends AdminController
{

    /**
     * 资源管理
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
        $page_index  = $this->getRequest()->getQuery('pageIndex');
        $page_size   = $this->getRequest()->getQuery('pageSize', 20);
        $sort        = $this->getRequest()->getQuery('sort', '');
        $sort_option = $this->formatSort($sort, array('resource_id', 'resource_type', 'menu_sort'));

        $search_params = array(
            'resource_name' => $this->getRequest()->getQuery('resource_name', ''),
            'resource_type' => $this->getRequest()->getQuery('resource_type', ''),
            'resource_key'  => $this->getRequest()->getQuery('resource_key', ''),
        );

        $ResourceModel  = ResourceModel::getInstance();
        $resource_count = $ResourceModel->getPageCount($search_params);
        $resource_list  = $resource_count ? $ResourceModel->getPageList($search_params, $page_size, $page_index, $sort_option) : array();
        $this->getResponse()->setBody(json_encode(array(
            'total' => $resource_count,
            'data'  => $resource_list,
        )));
    }

    /**
     * 根据URI生产resource key
     * @param string $uri
     * @return string
     */
    private function makeResourceKeyByUri($uri)
    {
        $resource_key = '';
        $tmp          = explode('/', trim($uri, '/'));
        $resource_key .= isset($tmp[0]) ? ucfirst($tmp[0]) : 'Index';
        $resource_key .= '-' . (isset($tmp[1]) ? ucfirst($tmp[1]) : 'Index');
        $resource_key .= '-' . (isset($tmp[2]) ? strtolower($tmp[2]) : 'index');
        return $resource_key;
    }

    /**
     * 检查资源ID是否存在
     * @param string $resource_id
     * @return bool
     */
    public function existResourceID($resource_id)
    {
        if (!empty($resource_id))
        {
            $ResourceModel = ResourceModel::getInstance();
            return $ResourceModel->getCount('resource_id = :resource_id', array(
                ':resource_id' => $resource_id
            )) ? true : false;
        }
        return true;
    }

    /**
     * 检查资源URI是否唯一
     * @param string $resource_uri
     * @return bool
     */
    public function uniqueResourceURI($resource_uri)
    {
        $condition = 'resource_uri = :resource_uri';
        $bind      = [':resource_uri' => $resource_uri];

        $resource_id = $this->getRequest()->getQuery('id');
        if (!empty($resource_id))
        {
            $condition .= ' and resource_id != :resource_id';
            $bind += [':resource_id' => $resource_id];
        }
        $ResourceModel = ResourceModel::getInstance();
        return $ResourceModel->getCount($condition, $bind) ? false : true;
    }

    /**
     * 添加资源
     */
    public function addResourceAction()
    {
        if ($this->getRequest()->isPost())
        {
            $post_data = $this->getRequest()->getPost();
            try
            {
                v::keySet(
                    v::key("resource_pid", v::allOf(
                        v::callback([$this, 'existResourceID'])
                    )),
                    v::key("resource_name", v::stringType()->length(null, 30)),
                    v::key("resource_type", v::in(ResourceModel::TYPE_LIST)),
                    v::key("menu_sort", v::intVal()->length(null, 10)),
                    v::key("menu_icon_class", v::stringType()->length(null, 60)),
                    v::key("resource_uri", v::allOf(
                        v::stringType()->length(1, 60),
                        v::callback([$this, 'uniqueResourceURI'])->setName('resource_uri unique')
                    )),
                    v::key("enable", v::in(array(YES, NO))),
                    v::key("public", v::in(array(YES, NO))),
                    v::key("login", v::in(array(YES, NO)))
                )->validate($post_data);
            }
            catch (ValidationException $e)
            {
                $this->responseJson(1001, $e->getMainMessage());
                return;
            }

            // 插入
            $ResourceModel = ResourceModel::getInstance();
            $params        = array(
                'resource_pid'    => $post_data['resource_pid'],
                'resource_type'   => $post_data['resource_type'],
                'resource_name'   => $post_data['resource_name'],
                'resource_key'    => $this->makeResourceKeyByUri($post_data['resource_uri']),
                'resource_uri'    => $post_data['resource_uri'],
                'menu_icon_class' => $post_data['menu_icon_class'],
                'enable'          => $post_data['enable'],
                'login'           => $post_data['login'],
                'public'          => $post_data['public'],
                'menu_sort'       => $post_data['menu_sort'],
                'create_time'     => \Db\DbString::prepare("now()"),
            );
            $ResourceModel->insert($params);
            $this->responseJson(0, 'success');
        }
        else
        {
            $ResourceModel = ResourceModel::getInstance();

            $assign = array(
                'resource_list' => $ResourceModel->getAll(array('resource_id', 'resource_name'), "resource_type = :resource_type",[
                    ':resource_type' => ResourceModel::TYPE_MENU
                ], 'menu_sort'),
                'request_uri'   => $this->getRequest()->getRequestUri(),
            );
            $this->display('addResource', $assign);
        }
    }

    public function editResourceAction()
    {
        $resource_id   = $this->getRequest()->getQuery('id');
        $ResourceModel = ResourceModel::getInstance();
        $resource_info = $ResourceModel->getInfo('resource_id', $resource_id, '*');
        if (empty($resource_info))
        {
            echo "资源ID[{$resource_id}]不存在";
            return;
        }

        if ($this->getRequest()->isPost())
        {
            $post_data = $this->getRequest()->getPost();
            try
            {
                v::keySet(
                    v::key("resource_pid", v::allOf(
                        v::callback([$this, 'existResourceID'])
                    )),
                    v::key("resource_name", v::stringType()->length(null, 30)),
                    v::key("resource_type", v::in(ResourceModel::TYPE_LIST)),
                    v::key("menu_sort", v::intVal()->length(null, 10)),
                    v::key("menu_icon_class", v::stringType()->length(null, 60)),
                    v::key("resource_uri", v::allOf(
                        v::stringType()->length(1, 60),
                        v::callback([$this, 'uniqueResourceURI'])->setName('resource_uri unique')
                    )),
                    v::key("enable", v::in(array(YES, NO))),
                    v::key("public", v::in(array(YES, NO))),
                    v::key("login", v::in(array(YES, NO)))
                )->check($post_data);
            }
            catch (ValidationException $e)
            {
                $this->responseJson(1001, $e->getMainMessage());
                return;
            }

            // 更新
            $ResourceModel = ResourceModel::getInstance();
            $params        = array(
                'resource_pid'    => $post_data['resource_pid'],
                'resource_type'   => $post_data['resource_type'],
                'resource_name'   => $post_data['resource_name'],
                'resource_key'    => $this->makeResourceKeyByUri($post_data['resource_uri']),
                'resource_uri'    => $post_data['resource_uri'],
                'menu_icon_class' => $post_data['menu_icon_class'],
                'enable'          => $post_data['enable'],
                'login'           => $post_data['login'],
                'public'          => $post_data['public'],
                'menu_sort'       => $post_data['menu_sort'],
            );
            $ResourceModel->update($params, 'resource_id = :resource_id', [':resource_id' => $resource_id]);
            $this->responseJson(0, 'success');
        }
        else
        {
            $assign = array(
                'resource_info' => $resource_info,
                'resource_list' => $ResourceModel->getAll(array('resource_id', 'resource_name'), "resource_type = :resource_type",[
                    ':resource_type' => ResourceModel::TYPE_MENU
                ], 'menu_sort'),
                'request_uri'   => $this->getRequest()->getRequestUri() . '?id=' . $resource_id,
            );
            $this->display('editResource', $assign);
        }
    }
}
