<?php

declare(strict_types = 1);
namespace Main\Core;
defined('IN_SYS') || exit('ACC Denied');

use Closure;
/**
 * 中间件父类
 */
abstract class Middleware {
    // 路由别名排除
    protected $except = []; 
    
    final public function implement(Closure $next){
        // 前置中间件
        $this->doHandle();
        
        // 传递
        $response = $next();
        
        // 末置中间件
        $newResponse = $this->doTerminate($response);

        // 返回
//        return is_null($newResponse) ? $response : $newResponse;
        return $newResponse;
    }
    
    /**
     * 执行前置中间件
     */
    final protected function doHandle(){
        if($this->effective() && method_exists($this, 'handle')){
            Integrator::run($this, 'handle');
        }
    }
    
    /**
     * 执行末置中间件
     * @param type $response    上级操作响应结果
     * @return mix              本次操作的响应 ( 合理的返回不应该为 null , null 将会忽略 )
     */
    final protected function doTerminate($response){
        if($this->effective() && method_exists($this, 'terminate')){
            return Integrator::run($this, 'terminate',[$response]);
        }else
            return $response;
    }
    
    /**
     * 是否执行
     * @return bool
     */
    final protected function effective() : bool{
        return !\in_array(Route::getAlias(), $this->except);
    }

}