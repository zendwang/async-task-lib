<?php
namespace Asynclib\Amq;

use PhpAmqpLib\Connection\AMQPStreamConnection;
use Asynclib\Exception\ConnectionedException;
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
    public static function factory() {
        if (is_null(self::$amq_connection)) {
            self::$amq_connection = self::getConnection();
        }

        return self::$amq_connection;
    }

    private static function getConnection(){
        try{
            return new AMQPStreamConnection(self::$amq_host, self::$amq_port, self::$amq_user, self::$amq_pass, self::$amq_vhost);
        }catch (\Exception $exc){
            throw new ConnectionedException($exc->getMessage());
        }
    }
}
