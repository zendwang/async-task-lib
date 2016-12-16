<?php
namespace Asynclib\Core;

use Asynclib\Amq\Queue;
use Asynclib\Amq\Exchange;
use Asynclib\Amq\AmqFactory;
use PhpAmqpLib\Wire\AMQPTable;
class DeadLetter{

    use Exchange, Queue;

    private $letter_exchange;
    private $letter_routing_key;

    public function setLetterRoutingKey($letter_routing_key) {
        $this->letter_routing_key = $letter_routing_key;
    }

    public function setLetterExchange($letter_exchange) {
        $this->letter_exchange = $letter_exchange;
    }

    private function getLetterExchange() {
        return $this->letter_exchange;
    }

    private function getLetterRoutingKey() {
        return $this->letter_routing_key;
    }

    public function init(){
        $connection = AmqFactory::factory();
        $channel = $connection->channel();
        $data['x-dead-letter-exchange'] = $this->getLetterExchange();
        $data['x-dead-letter-routing-key'] = $this->getLetterRoutingKey();
        $channel->queue_declare($this->getQueueName(), false, true, false, false, false, new AMQPTable($data));
        $channel->exchange_declare($this->getExchangeName(), $this->getExchangeType(), false, true, false, false, false);
        foreach ($this->getRoutingKeys() as $routing_key){
            $channel->queue_bind($this->getQueueName(), $this->getExchangeName(), $routing_key);
        }
    }
}