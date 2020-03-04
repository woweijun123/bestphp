<?php
/**
 * 入口文件
 * @Author   Wcj
 * @email 1054487195@qq.com
 * @DateTime 2020/3/3 14:29
 */
// 应用目录为当前目录
use bestphp\Bestphp;

define('APP_PATH', __DIR__ . DIRECTORY_SEPARATOR);
// 开启调试模式
define('APP_DEBUG', true);
// 加载框架文件
require(APP_PATH . 'bestphp/Bestphp.php');
// 加载配置文件
$config = require(APP_PATH . 'config/config.php');
// 实例化框架类
(new Bestphp($config))->run();