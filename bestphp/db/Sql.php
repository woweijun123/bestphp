<?php

namespace bestphp\db;

use \PDOStatement;

/**
 * Sql
 * @Author   Wcj
 * @email 1054487195@qq.com
 * @DateTime 2020/3/3 17:35
 */
class Sql
{
    // 数据库表名
    protected $table;
    // 数据库主键
    protected $primary = 'id';
    // where和order拼装后的条件
    private $filter = '';
    // Pdo bindParam()绑定的参数集合
    private $param = [];

    /**
     * 查询条件拼接，使用方式：
     *
     * $this->where(['id = 1','and title="Web"', ...])->fetch();
     * 为防止注入，建议通过$param方式传入参数：
     * $this->where(['id = :id'], [':id' => $id])->fetch();
     *
     * @param array $where 条件
     * @return $this 当前对象
     */
    public function where($where = [], $param = [])
    {
        if ($where) {
            $this->filter .= ' WHERE ';
            $this->filter .= implode(' ', $where);
            $this->param = $param;
        }
        return $this;
    }

    /**
     * 拼装排序条件，使用方式：
     *
     * $this->order(['id DESC', 'title ASC', ...])->fetch();
     *
     * @param array $order 排序条件
     * @return $this
     * @Author Wcj
     * @email 1054487195@qq.com
     * @DateTime 2020/3/4 14:23
     * @return $this
     */
    public function order($order = [])
    {
        if ($order) {
            $this->filter .= ' ORDER BY ';
            $this->filter .= implode(',', $order);
        }

        return $this;
    }

    /**
     * 查询所有
     * @Author Wcj
     * @email 1054487195@qq.com
     * @DateTime 2020/3/4 14:26
     */
    public function fetchAll()
    {
        $sql = sprintf("select * from `%s` %s", $this->table, $this->filter);
        $sth = Db::pdo()->prepare($sql);
        $sth = $this->formatParam($sth, $this->param);
        $sth->execute();

        return $sth->fetchAll();
    }

    /**
     * 查询一条
     * @Author Wcj
     * @email 1054487195@qq.com
     * @DateTime 2020/3/4 14:31
     */
    public function fetch()
    {
        $sql = sprintf("select * from `%s` %s", $this->table, $this->filter);
        $sth = Db::pdo()->prepare($sql);
        $sth = $this->formatParam($sth, $this->param);
        $sth->execute();

        return $sth->fetch();
    }

    /**
     * 根据条件(id)删除
     * @Author Wcj
     * @email 1054487195@qq.com
     * @DateTime 2020/3/4 15:18
     * @param $id
     * @return int
     */
    public function delete($id)
    {
        $sql = sprintf("delete from `%s` where `%s` = :%s", $this->table, $this->primary, $this->primary);
        $sth = Db::pdo()->prepare($sql);
        $sth = $this->formatParam($sth, [$this->primary => $id]);
        $sth->execute();

        return $sth->rowCount();
    }

    /**
     * 新增数据
     * @Author Wcj
     * @email 1054487195@qq.com
     * @DateTime 2020/3/4 15:24
     * @param $data
     * @return int
     */
    public function add($data)
    {
        $sql = sprintf("insert into `%s` %s", $this->table, $this->formatInsert($data));
        $sth = Db::pdo()->prepare($sql);
        $sth = $this->formatParam($sth, $data);
        $sth = $this->formatParam($sth, $this->param);
        $sth->execute();

        return $sth->rowCount();
    }

    /**
     * 修改数据
     * @Author Wcj
     * @email 1054487195@qq.com
     * @DateTime 2020/3/4 15:58
     */
    public function update($data)
    {
        $sql = sprintf("update `%s` set %s %s", $this->table, $this->formatUpdate($data), $this->filter);
        $sth = Db::pdo()->prepare($sql);
        $sth = $this->formatParam($sth, $data);
        $sth = $this->formatParam($sth, $this->param);
        $sth->execute();

        return $sth->rowCount();
    }

    /**
     * 占位符绑定具体的变量值
     * @param PDOStatement $sth 要绑定的PDOStatement对象
     * @param array $params 参数，有三种类型：
     * 1）如果SQL语句用问号?占位符，那么$params应该为
     *    [$a, $b, $c]
     * 2）如果SQL语句用冒号:占位符，那么$params应该为
     *    ['a' => $a, 'b' => $b, 'c' => $c]
     *    或者
     *    [':a' => $a, ':b' => $b, ':c' => $c]
     *
     * @return PDOStatement
     * @Author Wcj
     * @email 1054487195@qq.com
     * @DateTime 2020/3/4 14:35
     * @param $sth
     * @param array $param
     */
    public function formatParam(PDOStatement $sth, array $params = [])
    {
        foreach ($params as $param => &$value) {
            $param = is_int($param) ? $param + 1 : ':' . ltrim($param, ':');
            $sth->bindParam($param, $value); // TODO 绑定参数需要加:吗？
        }

        return $sth;
    }

    /**
     * 将数组转换成插入格式的sql语句
     * @Author Wcj
     * @email 1054487195@qq.com
     * @DateTime 2020/3/4 15:27
     */
    private function formatInsert($data)
    {
        // TODO 待测试
        $fields = [];
        $names = [];
        foreach ($data as $key => $value) {
            $fields[] = sprintf("`%s`", $key);
            $names[] = sprintf(":%s", $key);
        }

        $field = implode(',', $fields);
        $name = implode(',', $names);

        return sprintf('(%s) values (%s)', $field, $name);
    }

    /**
     * 将数组转换成更新格式的sql语句
     * @Author Wcj
     * @email 1054487195@qq.com
     * @DateTime 2020/3/4 15:56
     * @param $data
     * @return string
     */
    private function formatUpdate($data)
    {
        // TODO 待测试
        $fileds = [];
        foreach ($data as $key => $value) {
            $fields[] = sprintf("`%s` = :%s", $key, $key);
        }

        return implode(',', $fields);
    }
}