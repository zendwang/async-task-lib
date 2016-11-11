<?php
namespace Asynclib\Core;

/**
 * RabbitMQ工厂类
 * @author yanbo
 */
use PhpAmqpLib\Connection\AMQPStreamConnection;
class AmqFactory {

//    const AMQ_HOST = AMQ_HOST;
//    const AMQ_PORT = AMQ_PORT;
//    const AMQ_USER = AMQ_USER;
//    const AMQ_PASS = AMQ_PASS;
//    const AMQ_VHOST = AMQ_VHOST;
    private static  $_connection = NULL;

    /**
     * 静态工厂方法，返还此类的唯一实例
     */
    public static function getConnection() {
        if (is_null(self::$_connection)) {
            self::$_connection = new AMQPStreamConnection('192.168.100.60', 5672, 'guest', 'guest', 'shzf');
        }

        return self::$_connection;
    }
}
