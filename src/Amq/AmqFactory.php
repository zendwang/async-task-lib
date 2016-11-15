<?php
namespace Asynclib\Amq;

/**
 * RabbitMQ工厂类
 * @author yanbo
 */
use PhpAmqpLib\Connection\AMQPStreamConnection;
class AmqFactory {

    private static $amq_host = AMQ_HOST;
    private static $amq_port = AMQ_PORT;
    private static $amq_user = AMQ_USER;
    private static $amq_pass = AMQ_PASS;
    private static $amq_vhost = AMQ_VHOST;
    private static $amq_connection = NULL;

    /**
     * 静态工厂方法，返还此类的唯一实例
     */
    public static function getConnection() {
        if (is_null(self::$amq_connection)) {
            self::$amq_connection = new AMQPStreamConnection(self::$amq_host, self::$amq_port, self::$amq_user, self::$amq_pass, self::$amq_vhost);
        }

        return self::$amq_connection;
    }
}
