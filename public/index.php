<?php
var_dump(ob_get_level());
/*
  |--------------------------------------------------------------------------
  | 入口文件,IN_SYS 可以被用来做一些有趣的事情
  |--------------------------------------------------------------------------
  | init.php 作为初始化
  |
 */
defined('IN_SYS') || define('IN_SYS', substr(str_replace('\\', '/', __FILE__), strrpos(str_replace('\\', '/', __FILE__), '/') + 1));

require '../init.php';
