<?php
namespace Asynclib\Consumer;

/**
 * Scheduler
 * @author yanbo
 */
use Asynclib\Producer\Publish;
class Scheduler {

    public static $event_key = 'ebats_core_event';
    private static $task_key = 'ebats_core_task';
    private $tasks;

    public function setTasks($tasks = array()){
        $this->tasks = $tasks;
    }

    private function getTasks($event){
        return isset($this->tasks[$event]) ? $this->tasks[$event] : array();
    }

    private function getEvents(){
        return array_keys($this->tasks);
    }

    public function run() {
        $process = function($event, $msg){
            foreach ($this->getTasks($event) as $task){
                $task['params'] = $msg;

                $publish = new Publish();
                $publish->setExchange(self::$task_key);
                $publish->send($task, $task['topic'], false);
            }
        };
        $worker = new Worker($process);
        $worker->setExchange(self::$event_key);
        $worker->setQueue(self::$event_key, $this->getEvents());
        $worker->start();
    }
}