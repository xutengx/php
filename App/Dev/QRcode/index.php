<?php

namespace App\Dev\QRcode;
/**
 * 2维码
 */
class index{  
    public function index(\QRcode $QRcode){
        $QRcode->pixelSize = 11;
//        $QRcode->cachefile = ROOT.'data/Cache/qwewqewqewq.png';
        echo '<img src="'.\QRcode::base64('https://www.baidu.com').'" />';
        exit;
    }
}