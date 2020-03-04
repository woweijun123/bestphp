<?php
if (!function_exists('p')) {
    /**
     * 打印输出数据
     * @Author   Wcj
     * @DateTime 2018/11/29 13:40
     * @param $var
     */
    function p($var)
    {
        if (is_bool($var)) {
            var_dump($var);
        } else if (is_null($var)) {
            var_dump(NULL);
        } else {
            echo "<pre style='padding:10px;border-radius:5px;background:#F5F5F5;border:1px solid #aaa;font-size:14px;line-height:18px;'>" . print_r($var, true) . "</pre>";
        }

        exit;
    }
}