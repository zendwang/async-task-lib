<?php
namespace Asynclib\Amq;

trait Queue {

    private $queue_name;
    private $routing_keys;

    public function setQueue($name, $routing_keys = array('')) {
        $this->queue_name = $name;
        $this->routing_keys = $routing_keys;
    }

    private function getQueueName() {
        return $this->queue_name;
    }

    private function getRoutingKeys() {
        return $this->routing_keys;
    }
}