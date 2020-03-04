<?php
namespace bestphp\base;

/**
 * 控制器基类
 * @Author   Wcj
 * @email 1054487195@qq.com
 * @DateTime 2020/3/3 17:34
 */
class Controller
{
    protected $_controller;
    protected $_action;
    protected $_view;

    /**
     * 构造函数, 初始化属性, 并实例化对应模型
     * @Author Wcj
     * @email 1054487195@qq.com
     * @DateTime 2020/3/4 14:08
     * @param $controller
     * @param $action
     */
    public function __construct($controller, $action)
    {
        $this->_controller = $controller;
        $this->_action = $action;
        $this->_view = new View($controller, $action);
    }

    /**
     * 分配变量
     * @Author Wcj
     * @email 1054487195@qq.com
     * @DateTime 2020/3/4 14:08
     */
    public function assign($name, $value)
    {
        $this->_view->assign($name, $value);
    }

    /**
     * 渲染视图
     * @Author Wcj
     * @email 1054487195@qq.com
     * @DateTime 2020/3/4 14:09
     */
    public function render()
    {
        $this->_view->render();
    }
}