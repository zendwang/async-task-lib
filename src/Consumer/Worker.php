<?php
namespace Asynclib\Consumer;

/**
 * Worker工作类
 * @author yanbo
 */
use Asynclib\Core\AmqFactory;
class Worker {

    private $process;
    private $process_num;
    private $queue;
    private $exchange;

    public function __construct($process, $process_num = 1) {
        $this->process = $process;
        $this->process_num = $process_num;
    }

    public function setQueue($queue) {
        $this->queue = $queue;
    }

    public function setExchage($exchage) {
        $this->exchange = $exchage;
    }

    private function getQueue() {
        return $this->queue;
    }

    private function getExchange() {
        return $this->exchange;
    }

    public function _process(){
        $queue = $this->getQueue();
        $exchange = $this->getExchange();
        try{
            $connection = AmqFactory::getConnection();
            $channel = $connection->channel();
            $channel->queue_declare($queue, false, true, false, false);
            if ($exchange){
                $channel->exchange_declare($exchange, 'direct', false, true, false);
                $channel->queue_bind($queue, $exchange);
            }
        }catch (\Exception $e){
            echo $e->getMessage();
            exit;
        }

        $callback = function($message){
            $routing_key = $message->delivery_info['routing_key'];
            call_user_func($this->process, $routing_key, $message->getBody());
            $message->delivery_info['channel']->basic_ack($message->delivery_info['delivery_tag']);
        };
        $channel->basic_consume($queue, '', false, false, false, false, $callback);
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