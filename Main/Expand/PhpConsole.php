<?php

declare(strict_types = 1);
namespace Main\Expand;

use Main\Core\Conf;
use PhpConsole\Handler;
use PhpConsole\Connector;

/**
 * 借助谷歌浏览器的 php console 插件, 以及 php-console 包, 进行调试
 * @methor debug        (mix, string);
 */
class PhpConsole {

    private $handle;
    
    public function __construct() {
        $conf = obj(Conf::class)->phpconsole;
        if(!is_null($conf['passwd'])){
//            Connector::setPostponeStorage(new Session());
            $connector = Connector::getInstance();
//            $connector->setHeadersLimit(200000);
            $connector->setPassword($conf['passwd']);
        }
        $this->handle = Handler::getInstance();
    }
  
    public function __call(string $func, array $params){
        return call_user_func_array([$this->handle, $func], $params);
    }
}