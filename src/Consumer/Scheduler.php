<?php
namespace Asynclib\Consumer;

/**
 * Scheduler
 * @author yanbo
 */
use Asynclib\Producer\Publish;
class Scheduler {

    const EXCHANGE_EVENT = 'ebats_core_event';
    const EXCHANGE_TASK = 'ebats_core_task';
    const QUEUE_EVENT = 'ebats_core_event';

    public function run() {
        $process = function($event, $msg){
            foreach (EventManager::getTasks($event) as $task){
                $data = [
                    'name' => $task['name'],
                    'params' => json_decode($msg, 1),
                ];
                $publish = new Publish();
                $publish->setExchange(self::EXCHANGE_TASK);
                $publish->send($data, $task['topic'], false);
            }
        };
        $worker = new Worker($process);
        $worker->setExchange(self::EXCHANGE_EVENT);
        $worker->setQueue(self::QUEUE_EVENT, EventManager::getEvents());
        $worker->start();
    }
}