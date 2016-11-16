<?php
namespace Asynclib\Producer;

/**
 * Worker工作类
 * @author yanbo
 */
use Asynclib\Amq\AmqFactory;
use Asynclib\Amq\Exchange;
use PhpAmqpLib\Message\AMQPMessage;
class Publish {

    use Exchange;

    public function send($data, $routing_key = '', $close_connection = true){
        $connection = AmqFactory::getConnection();
        $channel = $connection->channel();
        $channel->exchange_declare($this->getExchangeName(), $this->getExchangeType(), false, true, false);

        $toSend = new AMQPMessage(json_encode($data), array('content_type' => 'text/plain', 'delivery_mode' => 2));
        $channel->basic_publish($toSend, $this->getExchangeName(), $routing_key);
        $channel->close();
        if ($close_connection){
            $connection->close();
        }
    }
}