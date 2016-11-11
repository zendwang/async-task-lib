<?php
namespace Asynclib\Producer;

/**
 * Worker工作类
 * @author yanbo
 */
use Asynclib\AmqFactory;
use PhpAmqpLib\Message\AMQPMessage;
class Publish {

    private $exchange;
    private $exchange_type;

    public function __construct() {
    }

    /**
     * 设置转发器
     * @param string $name 名称
     * @param string $type 类型 fanout,direct,topic 默认为direct
     */
    public function setExchage($name, $type = 'direct') {
        $this->exchange = $name;
        $this->exchange_type = $type;
    }

    private function getExchange() {
        return $this->exchange;
    }

    private function getExchangeType() {
        return $this->exchange_type;
    }

    public function send($data, $routing_key = ''){
        $connection = AmqFactory::getConnection();
        $channel = $connection->channel();
        $channel->exchange_declare($this->getExchange(), $this->getExchangeType(), false, true, false);

        $toSend = new AMQPMessage(json_encode($data), array('content_type' => 'text/plain', 'delivery_mode' => 2));
        $channel->basic_publish($toSend, $this->getExchange(), $routing_key);
        $channel->close();
        $connection->close();
    }
}