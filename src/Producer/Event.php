<?php
namespace Asynclib\Producer;


use Asynclib\Consumer\Scheduler;

class Event {

    private $event;
    private $params;

    public function __construct($event_name) {
        $this->event = $event_name;
    }

    public function setOptions($options = array()){
        $this->params = $options;
    }

    public function getEvent() {
        return $this->event;
    }

    public function getParams() {
        return json_encode($this->params);
    }

    public function publish() {
        $worker = new Publish();
        $worker->setExchange(Scheduler::$event_key);
        $worker->send($this->getParams(), $this->getEvent(), false);
    }
}