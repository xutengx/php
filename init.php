<?php

declare(strict_types = 1);

$GLOBALS['statistic'] = [
	'时间初始量'	 => microtime(true),
	'内存初始量'	 => memory_get_usage()
];
/*
  |--------------------------------------------------------------------------
  | 入口文件目录在服务器的绝对路径
  |--------------------------------------------------------------------------
  |
  | eg:/mnt/hgfs/www/git/php_/project/
  |
 */
define('ROOT', str_replace('\\', '/', __DIR__) . '/');

/*
  |--------------------------------------------------------------------------
  | composer 自动加载
  |--------------------------------------------------------------------------
  |
 */
require ROOT . 'vendor/autoload.php';

/*
  |--------------------------------------------------------------------------
  | 定义全局常量
  |--------------------------------------------------------------------------
  |
 */
require ROOT . 'bootstrap/define.php';

/*
  |--------------------------------------------------------------------------
  | 返回内核容器
  |--------------------------------------------------------------------------
  |
  | 注册`gaara`的自带服务
  | 注册业务的服务
  |
 */
$app = require ROOT . 'bootstrap/app.php';

/*
  |--------------------------------------------------------------------------
  | 初始化请求, 并执行
  |--------------------------------------------------------------------------
  |
  | 有问题请联系 QQ 68822684
  |
 */

$app->init()->start();
