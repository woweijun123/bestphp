<?php
namespace bestphp\base;

use bestphp\db\Sql;

/**
 * Model
 * @Author   Wcj
 * @email 1054487195@qq.com
 * @DateTime 2020/3/3 17:34
 */
class Model extends Sql
{
    protected $model;

    public function __construct()
    {
        if (!$this->table) {
            // 获取模型类名称
            $this->model = get_class($this);
            // 删除类名最后的Model字符
            $this->model = substr($this->model, 0, -5);
            // 数据库表名与类名一致
            $this->table = strtolower($this->model);
        }
    }
}