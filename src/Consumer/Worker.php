<?php
namespace Asynclib\Consumer;

/**
 * Worker工作类
 * @author yanbo
 */
use Asynclib\Amq\Exchange;
use Asynclib\Amq\Queue;
use Asynclib\Amq\AmqFactory;
class Worker {

    use Queue, Exchange;
    private $process;
    private $process_num;

    public function __construct($process, $process_num = PROCESS_DEFAULT_NUM) {
        $this->process = $process;
        $this->process_num = $process_num;
    }

    public function _process(){
        $connection = AmqFactory::getConnection();
        $channel = $connection->channel();
        $channel->queue_declare($this->getQueueName(), false, true, false, false);
        if ($this->existsExchange()){
            $channel->exchange_declare($this->getExchangeName(), $this->getExchangeType(), false, true, false, false, false, $this->getArguments());
            foreach ($this->getRoutingKeys() as $routing_key){
                $channel->queue_bind($this->getQueueName(), $this->getExchangeName(), $routing_key);
            }
        }

        $callback = function($message){
            $routing_key = $message->delivery_info['routing_key'];
            call_user_func($this->process, $routing_key, $message->getBody());
            $message->delivery_info['channel']->basic_ack($message->delivery_info['delivery_tag']);
        };
        $channel->basic_consume($this->getQueueName(), '', false, false, false, false, $callback);
        while(count($channel->callbacks)) {
            $channel->wait();
        }

        $channel->close();
        $connection->close();
    }

    public function start(){
        $workers = [];
        for($i = 0; $i < $this->process_num; $i++){
            $process = new \swoole_process([$this, '_process']);
            $pid = $process->start();
            $workers[$pid] = $process;
        }
        \swoole_process::wait();
    }
}