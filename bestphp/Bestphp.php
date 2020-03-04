<?php

namespace bestphp;

// 框架根目录
defined('CORE_PATH') or define('CORE_PATH', __DIR__);

/**
 * bestphp框架核心
 * @Author   Wcj
 * @email 1054487195@qq.com
 * @DateTime 2020/3/3 14:29
 */
class Bestphp
{
    // 配置
    protected $config;

    public function __construct($config = [])
    {
        $this->config = $config;
    }

    public function run()
    {
        spl_autoload_register([$this, 'loadClass']); // 注册自动加载
        $this->setReporting(); // 检测开发环境
        $this->removeMagicQuotes(); // 检测并删除敏感字符
        $this->unregisterGlobals(); // 检测自定义全局变量并移除
        $this->setDbConfig(); // 配置数据库信息
        $this->route(); // 路由处理
    }

    /**
     * 自动加载类
     * @Author Wcj
     * @email 1054487195@qq.com
     * @DateTime 2020/3/3 17:31
     */
    public function loadClass($className)
    {
        $classMap = $this->classMap();

        if (isset($classMap[$className])) {
            // 包含内核文件
            $file = $classMap[$className];
        } elseif (strpos($className, '\\') !== false) {
            // 包含应用(application目录)文件
            $file = APP_PATH . strtr($className, '\\', '/') . '.php';
            if (!is_file($file)) return;
        } else {
            return;
        }
        include $file;
        // 这里可以加入判断，如果名为$className的类、接口不存在，则在调试模式下抛出错误
    }

    /**
     * 内核文件命名空间映射关系
     * @Author Wcj
     * @email 1054487195@qq.com
     * @DateTime 2020/3/3 17:34
     * @return array
     */
    public function classMap()
    {
        return [
            'bestphp\base\Controller' => CORE_PATH . '/base/Controller.php',
            'bestphp\base\Model' => CORE_PATH . '/base/Model.php',
            'bestphp\base\View' => CORE_PATH . '/base/View.php',
            'bestphp\db\Db' => CORE_PATH . '/db/Db.php',
            'bestphp\db\Sql' => CORE_PATH . '/db/Sql.php',
        ];
    }

    /**
     * 检测开发环境
     * @Author Wcj
     * @email 1054487195@qq.com
     * @DateTime 2020/3/3 15:31
     */
    public function setReporting()
    {
        if (APP_DEBUG === true) {
            error_reporting(E_ALL);
            // 错误回显，错误回显会暴露出非常多的敏感信息, 一般常用与开发环境。
            ini_set('display_errors', 'On');
        } else {
            error_reporting(E_ALL);
            ini_set('display_errors', 'Off');
            // 一旦开启了错误日志记录功能，个人强烈建议设置错误日志目录
            ini_set('log_errors', 'On'); // 错误日志
        }
    }

    /**
     * 检测并删除敏感字符
     * @Author Wcj
     * @email 1054487195@qq.com
     * @DateTime 2020/3/3 15:35
     */
    public function removeMagicQuotes()
    {
        if (get_magic_quotes_gpc()) {
            $_GET = isset($_GET) ? $this->stripSlashesDeep($_GET) : '';
            $_POST = isset($_POST) ? $this->stripSlashesDeep($_POST) : '';
            $_COOKIE = isset($_COOKIE) ? $this->stripSlashesDeep($_COOKIE) : '';
            $_SESSION = isset($_SESSION) ? $this->stripSlashesDeep($_SESSION) : '';
        }
    }

    /**
     * 删除敏感字符
     * @Author Wcj
     * @email 1054487195@qq.com
     * @DateTime 2020/3/3 16:45
     * @param $value
     * @return array|string
     */
    public function stripSlashesDeep($value)
    {
        return is_array($value) ? array_map([$this, 'stripSlashesDeep'], $value) : stripslashes($value);
    }

    /**
     * 检测自定义全局变量并移除
     * 因为 register_globals 已经弃用，如果已经弃用的 register_globals 指令被设置为 on，那么局部变量也将在脚本的全局作用域中可用。
     * 例如， $_POST['foo'] 也将以 $foo 的形式存在，这样写是不好的实现，会影响代码中的其他变量。
     * 相关信息，参考: http://php.net/manual/zh/faq.using.php#faq.register-globals
     * @Author Wcj
     * @email 1054487195@qq.com
     * @DateTime 2020/3/3 16:54
     */
    public function unregisterGlobals()
    {
        if (ini_get('register_globals')) {
            $array = ['_SESSION', '_POST', '_GET', '_COOKIE', '_REQUEST', '_SERVER', '_ENV', '_FILES'];
            foreach ($array as $value) {
                foreach ($GLOBALS[$value] as $key => $var) {
                    if ($var === $GLOBALS[$key]) {
                        unset($GLOBALS[$key]);
                    }
                }
            }
        }
    }

    /**
     * 配置数据库信息
     * @Author Wcj
     * @email 1054487195@qq.com
     * @DateTime 2020/3/3 16:59
     */
    public function setDbConfig()
    {
        if ($this->config['db']) {
            define('DB_HOST', $this->config['db']['host']);
            define('DB_NAME', $this->config['db']['dbname']);
            define('DB_USER', $this->config['db']['username']);
            define('DB_PASS', $this->config['db']['password']);
        }
    }

    /**
     * 路由处理
     * @Author Wcj
     * @email 1054487195@qq.com
     * @DateTime 2020/3/3 17:05
     */
    public function route()
    {
        $controllerName = $this->config['default_controller'];
        $actionName = $this->config['default_action'];
        $param = [];
        $url = $_SERVER['REQUEST_URL'];

        // 清除?之后的内容
        $position = strpos($url, '?');
        $url = $position === false ? $url : substr($url, $position); // TODO 待测试
        // 去除前后的正斜杠
        $url = trim($url, '/');
        if ($url) {
            $urlArray = explode('/', $url);
            $urlArray = array_filter($urlArray);
            // 获取控制器名
            $controllerName = ucfirst($urlArray['0']);
            // 获取方法名
            array_shift($urlArray);
            $actionName = $urlArray ? $urlArray['0'] : $actionName;
            // 获取url参数
            array_shift($urlArray);
            $param = $urlArray ? $urlArray : [];
        }
        // 判断控制器和操作是否存在
        $controller = 'app\\controllers\\' . $controllerName . 'Controller';
        if (!class_exists($controller)) {
            exit($controller . '控制器不存在');
        }
        if (!method_exists($controller, $actionName)) {
            exit($actionName . '方法不存在');
        }
        // 如果控制器和操作名存在，则实例化控制器，因为控制器对象里面
        // 还会用到控制器名和操作名，所以实例化的时候把他们俩的名称也
        // 传进去。结合Controller基类一起看
        $dispatch = new $controller($controllerName, $actionName);
        // $dispatch保存控制器实例化后的对象，我们就可以调用它的方法，
        // 也可以像方法中传入参数，以下等同于：$dispatch->$actionName($param)
        call_user_func_array([$dispatch, $actionName], $param);
    }
}