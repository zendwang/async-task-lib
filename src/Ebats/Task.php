<?php
namespace Asynclib\Ebats;
use Asynclib\Core\Publish;

class Task {

    private $id;
    private $name;
    private $topic;
    private $params;
    private $delay;

    public function __construct($topic, $name, $delay = 0) {
        $this->id = $this->setId($topic);
        $this->name = $name;
        $this->topic = $topic;
        $this->delay = $delay;
    }

    public function getId(){
        return $this->id;
    }

    public function setParams($params) {
        $this->params = $params;
    }

    public function getName() {
        return $this->name;
    }

    public function getParams(){
        return $this->params;
    }

    public function getDelay(){
        return $this->delay;
    }

    public function getTopic() {
        return $this->topic;
    }

    private function setId($topic) {
        return md5(uniqid($topic.mt_rand(), 1));
    }
}