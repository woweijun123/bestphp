<?php

namespace bestphp\base;

/**
 * 视图基类
 * @Author   Wcj
 * @email 1054487195@qq.com
 * @DateTime 2020/3/3 17:34
 */
class View
{
    protected $variables = [];
    protected $_controller;
    protected $_action;

    public function __construct($controller, $action)
    {
        $this->_controller = strtolower($controller);
        $this->_action = strtolower($action);
    }

    /**
     * 分配变量
     * @Author Wcj
     * @email 1054487195@qq.com
     * @DateTime 2020/3/4 16:50
     */
    public function assign($name, $value)
    {
        $this->variables[$name] = $value;
    }

    /**
     * 渲染显示
     * @Author Wcj
     * @email 1054487195@qq.com
     * @DateTime 2020/3/4 16:51
     */
    public function render()
    {
        extract($this->variables);
        $defaultHeader = APP_PATH . 'app/view/header.php';
        $defaultFooter = APP_PATH . 'app/view/footer.php';

        $controllerHeader = APP_PATH . 'app/view' . $this->_controller . '/header.php';
        $controllerFooter = APP_PATH . 'app/view' . $this->_controller . '/footer.php';
        $controllerLayout = APP_PATH . 'app/view/' . $this->_controller . '/' . $this->_action . '.php';
        // 页头文件
        if (is_file($controllerHeader)) {
            include $controllerHeader;
        } else {
            include $defaultHeader;
        }

        // 判断视图文件是否存在
        if (is_file($controllerLayout)) {
            include $controllerLayout;
        } else {
            echo "<h1>无法找到视图文件";
        }

        // 页脚文件
        if (is_file($controllerFooter)) {
            include $controllerFooter;
        } else {
            include $defaultFooter;
        }
    }
}