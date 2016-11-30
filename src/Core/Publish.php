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
        if ($this->auto_close){
            $this->connection->close();
        }
    }
}