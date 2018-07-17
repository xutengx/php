<?php

declare(strict_types = 1);

/*
  |--------------------------------------------------------------------------
  | 获取内核容器
  |--------------------------------------------------------------------------
  |
 */
$app = \App\Kernel::getInstance();

/*
  |--------------------------------------------------------------------------
  | 注册绑定
  |--------------------------------------------------------------------------
  |
  | $app->bind(string, string)			普通绑定
  | $app->bindOnce(string, string)		单次绑定
  | $app->singleton(string, string)		单例绑定
  |
 */


/*
  |--------------------------------------------------------------------------
  | 获取对象
  |--------------------------------------------------------------------------
  |
  | $app->make(string)
  |
 */


/*
  |--------------------------------------------------------------------------
  | 返回内核容器
  |--------------------------------------------------------------------------
  |
  |
 */
return $app;
