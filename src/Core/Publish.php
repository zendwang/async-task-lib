<?php
namespace Asynclib\Core;

use Asynclib\Amq\AmqFactory;
use Asynclib\Amq\Exchange;
use PhpAmqpLib\Message\AMQPMessage;
use PhpAmqpLib\Wire\AMQPTable;
class Publish {

    use Exchange;

    private $connection;
    private $auto_close = true;

    public function __construct() {
        $this->connection = AmqFactory::factory();
    }

    public function setAutoClose($auto_close) {
        $this->auto_close = $auto_close;
    }

    public function send($data, $routing_key = '', $delay = 0){
        $channel = $this->connection->channel();
        $channel->exchange_declare($this->getExchangeName(), $this->getExchangeType(), false, true, false);

        $raw_data = ['etime' => time() + $delay, 'body' => $data];
        $properties = ['content_type' => 'text/plain', 'delivery_mode' => AMQPMessage::DELIVERY_MODE_PERSISTENT];
        $toSend = new AMQPMessage(serialize($raw_data), $properties);
        if ($delay){
            $toSend->set('expiration', $delay);
        }
        $channel->basic_publish($toSend, $this->getExchangeName(), $routing_key);
        $channel->close();
    }

    public function __destruct() {
        if ($this->auto_close){
            $this->connection->close();
        }
    }
}