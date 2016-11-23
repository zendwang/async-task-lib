<?php
namespace Asynclib\Producer;

/**
 * Worker工作类
 * @author yanbo
 */
use Asynclib\Amq\AmqFactory;
use Asynclib\Amq\Exchange;
use PhpAmqpLib\Message\AMQPMessage;
use PhpAmqpLib\Wire\AMQPTable;

class Publish {

    use Exchange;

    private $connection;

    public function __construct() {
        $this->connection = AmqFactory::getConnection();
    }

    public function send($data, $routing_key = '', $delay = 0){
        $channel = $this->connection->channel();
        $channel->exchange_declare($this->getExchangeName(), $this->getExchangeType(), false, true, false);

        $properties = ['content_type' => 'text/plain', 'delivery_mode' => AMQPMessage::DELIVERY_MODE_PERSISTENT];
        if ($delay){
            $properties['application_headers'] = new AMQPTable(['x-delay' => $delay * 1000]);
        }
        $raw_data['body'] = $data;
        $raw_data['etime'] = time() + $delay;
        $toSend = new AMQPMessage(serialize($raw_data), $properties);
        $channel->basic_publish($toSend, $this->getExchangeName(), $routing_key);
        $channel->close();
    }

    public function __destruct() {
        $this->connection->close();
    }
}