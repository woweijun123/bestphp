<?php
/**
 * 入口文件
 * @Author   Wcj
 * @email 1054487195@qq.com
 * @DateTime 2020/3/3 14:29
 */

// 定义根目录
define('ROOT_PATH', __DIR__ . DIRECTORY_SEPARATOR);
// 定义应用目录
define('APP_PATH', __DIR__ . DIRECTORY_SEPARATOR . 'app' . DIRECTORY_SEPARATOR);
// 开启调试模式
define('APP_DEBUG', true);
// 加载框架文件
require(ROOT_PATH . 'bestphp/Bestphp.php');
// 实例化框架类
(new \bestphp\Bestphp())->run();