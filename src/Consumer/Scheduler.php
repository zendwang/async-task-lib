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

    public function run() {
        $process = function($event, $msg){
            foreach (EventManager::getTasks($event) as $task){
                $task['params'] = $msg;

                $publish = new Publish();
                $publish->setExchange(self::$task_key);
                $publish->send($task, $task['topic'], false);
            }
        };
        $worker = new Worker($process);
        $worker->setExchange(self::$event_key);
        $worker->setQueue(self::$event_key, EventManager::getEvents());
        $worker->start();
    }
}