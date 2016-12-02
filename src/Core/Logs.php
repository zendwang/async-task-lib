<?php
namespace Asynclib\Core;

class Logs {

    public static function info($message) {
        self::output('info', $message);
    }

    public static function warning($message) {
        self::output('warning', $message);
    }

    public static function error($message) {
        self::output('error', $message);
    }

    private static function output($level, $message){
        if (!defined('EBATS_DEBUG') ||  !EBATS_DEBUG){
            return;
        }

        switch ($level){
            case 'error':
                $font_color = '31m';
                break;
            case 'warning':
                $font_color = '33m';
                break;
            default:
                $font_color = '34m';
        }
        $time = date('m-d H:i:s');
        passthru("echo -e '\033[{$font_color}[$time] $message \033[0m'");
    }
}