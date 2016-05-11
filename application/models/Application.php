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
 * Description of Application
 *
 * @author kuangzhiqiang
 */
class ApplicationModel
{
    /**
     * @var Db\Mysql
     */
    protected $db;

    /**
     * @var string
     */
    protected $table;

    /**
     * @return static
     */
    static public function getInstance()
    {
        return new static();
    }

    /**
     * @return \Db\Mysql
     */
    public function getDb()
    {
        return $this->db;
    }

    /**
     * 获取信息
     * @param string $field 查询字段
     * @param string $value 字段值
     * @param string $fields 查询的列，可多个或*
     * @return array
     */
    public function getInfo($field, $value, $fields = "*")
    {
        $sql = "select {$fields} from {$this->table} where {$field} = :{$field}";
        return $this->db->fetchRow($sql, array(":{$field}" => $value));
    }

    /**
     * 插入数据（单表）
     * @param array $data
     */
    public function insert(array $data)
    {
        $this->db->insert($this->table, $data);
    }

    /**
     * 存在则不插入（单表）
     *
     * @param array $data
     */
    public function insertIgnore(array $data)
    {
        $this->db->insertIgnore($this->table, $data);
    }

    /**
     * 替换数据（单表）
     *
     * @param array $data
     */
    public function replace(array $data)
    {
        $this->db->replace($this->table, $data);
    }

    /**
     * 批量插入（单表）
     *
     * @param array $data 二维数组
     * @param array $fields 指定需要插入的值, 为空则使用$data中所有key
     * @param string $type insert|ignore|replace
     */
    public function insertMulti(array $data, array $fields = array(), $type = 'insert')
    {
        $this->db->multiInsert($this->table, $data, $fields, $type);
    }

    /**
     * 更新（单表）
     *
     * @param array $data 待更新的数据 array('key'=>'value')
     * @param string $condition 条件 key=:key
     * @param array $bind 绑定条件 array(':key'=>$key)
     */
    public function update(array $data, $condition, array $bind = array())
    {
        $this->db->update($this->table, $data, $condition, $bind);
    }

    /**
     * 更新多行记录
     * @param array $data 待更新的二维数组
     * @param string $index_field 用于判断的字段名，本字段需要在$data第二维中存在
     * @param string $condition
     * @param array $condata
     */
    public function updateMulti(array $data, $index_field, $condition, array $condata = array())
    {
        $this->db->updateMulti($this->table, $data, $index_field, $condition, $condata);
    }

    /**
     * 存在则根据$update_data更新，反则插入$insert_data
     *
     * @param array $insert_data
     * @param array $update_data
     */
    public function insertUpdate(array $insert_data, array $update_data)
    {
        $this->db->insertUpdate($this->table, $insert_data, $update_data);
    }

    /**
     * 存在则根据$update_data更新，反则插入$insert_data
     *
     * @param array $insert_data
     * @param array $insert_fields 指定需要插入的值, 为空则使用$data中所有key
     * @param array $update_data
     */
    public function insertUpdateMulti(array $insert_data, array $insert_fields, array $update_data)
    {
        $this->db->insertUpdateMulti($this->table, $insert_data, $insert_fields, $update_data);
    }

    /**
     * 删除（单表）
     * @param $condition
     * @param array $bind
     */
    public function delete($condition, array $bind = array())
    {
        $this->db->delete($this->table, $condition, $bind);
    }

    /**
     * 获取一个值（单表）
     *
     * @param string $field 需要查询的字段 key
     * @param string $condition 条件 key=:key
     * @param array $bind 绑定条件 array(':key'=>$key)
     * @return string
     */
    public function getOne($field, $condition = '', array $bind = array())
    {
        if (!empty($condition))
        {
            $condition = " WHERE " . $condition;
        }

        $sql = "SELECT {$field} FROM {$this->table} {$condition}";
        return $this->db->fetchOne($sql, $bind);
    }

    /**
     * 获取行数
     *
     * @param string $condition 条件 key=:key
     * @param array $bind 绑定条件 array(':key'=>$key)
     * @return string
     */
    public function getCount($condition = '', array $bind = array())
    {
        if (!empty($condition))
        {
            $condition = " WHERE " . $condition;
        }

        $sql = "SELECT count(1) FROM {$this->table} {$condition}";
        return $this->db->fetchOne($sql, $bind);
    }

    /**
     * 获取一行（单表）
     *
     * @param string|array $fields 需要查询的字段 key|array('key1', 'key2')
     * @param string $condition 条件 key=:key
     * @param array $bind 绑定条件 array(':key'=>$key)
     * @return mixed
     */
    public function getRow($fields = '*', $condition = '', array $bind = array())
    {
        if (is_array($fields))
        {
            $fields = '`' . implode('`,`', $fields) . '`';
        }
        if (!empty($condition))
        {
            $condition = " WHERE " . $condition;
        }

        $sql = "SELECT {$fields} FROM {$this->table} {$condition} LIMIT 1";
        return $this->db->fetchRow($sql, $bind);
    }

    /**
     * 获取一列（单表）
     *
     * @param string $field 需要查询的字段 key
     * @param string $condition 条件 key=:key
     * @param array $bind 绑定条件 array(':key'=>$key)
     * @return mixed
     */
    public function getCol($field, $condition = '', array $bind = array())
    {
        if (!empty($condition))
        {
            $condition = " WHERE " . $condition;
        }

        $sql = "SELECT {$field} FROM {$this->table} {$condition}";
        return $this->db->fetchCol($sql, $bind);
    }

    /**
     * 获取列表（单表）
     * @param string|array $fields 需要查询的字段 key|array('key1', 'key2')
     * @param string $condition 条件 key=:key
     * @param array $bind 绑定条件 array(':key'=>$key)
     * @param string $order 排序字段 field asc|desc
     * @return array
     */
    public function getAll($fields = '*', $condition = '', array $bind = array(), $order = '')
    {
        if (is_array($fields))
        {
            $fields = '`' . implode('`,`', $fields) . '`';
        }

        if (!empty($condition))
        {
            $condition = " WHERE " . $condition;
        }

        if (!empty($order))
        {
            $order = " ORDER BY {$order}";
        }

        $sql = "SELECT {$fields} FROM {$this->table} {$condition} {$order}";
        return $this->db->fetchAll($sql, $bind);
    }

    /**
     * 获取分组数据
     * @param string $fields 获取的字段
     * @param array $group_fields 用于分组的key, 多个代表多维
     * @param string $condition 条件 key=:key
     * @param array $bind array(':key'=>$key)
     * @param string $order
     * @return array
     */
    public function getMap($fields = '*', array $group_fields, $condition = '', array $bind = array(), $order = '')
    {
        if (is_array($fields))
        {
            $fields = '`' . implode('`,`', $fields) . '`';
        }

        if (!empty($condition))
        {
            $condition = " WHERE " . $condition;
        }

        if (!empty($order))
        {
            $order = " ORDER BY {$order}";
        }

        $sql = "SELECT {$fields} FROM {$this->table} {$condition} {$order}";
        return $this->db->fetchMap($sql, $bind, $group_fields);
    }

    public function lastInsertId()
    {
        return $this->db->lastInsertId();
    }

    public function rowCount()
    {
        return $this->db->rowCount();
    }

    public function begin()
    {
        return $this->db->begin();
    }

    public function commit()
    {
        return $this->db->commit();
    }

    public function rollback()
    {
        return $this->db->rollback();
    }
}
