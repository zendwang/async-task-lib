<?php
namespace Asynclib\Core;

class Utils {

    public static function debug($message) {
        if (defined('EBATS_DEBUG') && EBATS_DEBUG){
            echo "$message \n";
        }
    }
}