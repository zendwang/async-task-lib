<?php
namespace Asynclib\Ebats;

/**
 * Counter
 * @author yanbo
 */
use Asynclib\Core\Publish;
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
        return $this->params;
    }

    public function publish() {
        $worker = new Publish();
        $worker->setExchange(Scheduler::EXCHANGE_EVENT);
        $worker->send($this->getParams(), $this->getEvent(), false);
    }
}