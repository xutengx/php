<?php
namespace App\index\Api;
use \Main\Core\Controller;
defined('IN_SYS')||exit('ACC Denied');
class testApi extends Controller\RestController{
    public function get(array $data){
        echo 'i am get of test';
    }
    public function put(array $data){
        echo 'i am put of test';
    }
    public function post(array $data){
        echo 'i am post of test';
    }
    public function delete(array $data){
        echo 'i am delete of test';
    }
}