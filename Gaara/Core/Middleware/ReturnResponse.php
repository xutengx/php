<?php

declare(strict_types = 1);
namespace Gaara\Core\Middleware;

use Gaara\Core\Middleware;
use Gaara\Expand\PhpConsole;
use Response;
use Iterator;
use Generator;

/**
 * 统一响应处理
 * 移除意外输出, 使用PhpConsole调试
 */
class ReturnResponse extends Middleware {

    protected $except = [];

    /**
     * 初始化 PhpConsole, 其__construct 中启用了ob_start, 再手动启用ob_start, 确保header头不会提前发出
     * 一层ob的情况下当使用ob_end_clean关闭之后的内容若超过web_server(nginx)的输出缓冲大小(默认4k),将会被发送
     * 受限于http响应头大小,意外输出过多时(大于3000)将会写入文件, 详见\Gaara\Expand\PhpConsole
     * 
     * @param PhpConsole $PhpConsole
     */
    public function handle(PhpConsole $PhpConsole) {
        ob_start();
    }

    /**
     * 特殊处理 true/false/Iterator/Generator
     * @param type $response
     */
    public function terminate($response) {
        if ($response instanceof Generator) {
            return $this->generatorDecode($response);
        } elseif ($response === true) {
            Response::setStatus(200)->exitData();
        } elseif ($response === false) {
            Response::setStatus(400)->exitData();
        } elseif ($response instanceof Iterator) {
            $response = $this->iteratorDecode($response);
        }
        Response::exitData($response);
    }

    /**
     * 解码Iterator对象到数组
     * @param Iterator $obj
     * @return array
     */
    private function iteratorDecode(Iterator $obj): array {
        $arr = [];
        foreach ($obj as $k => $v) {
            $arr[$k] = $v;
        }
        return $arr;
    }

    /**
     * 解码Generator对象并直接输出
     * 一般用于大文件下载
     * @param Generator $obj
     * @return void
     */
    private function generatorDecode(Generator $obj): void {
        foreach ($obj as $v) {
            echo $v;
            ob_flush();
            flush();
        }
        exit;
    }
}
