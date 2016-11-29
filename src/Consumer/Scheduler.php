<?php
namespace Asynclib\Consumer;

/**
 * Scheduler
 * @author yanbo
 */
use Asynclib\Amq\ExchangeTypes;
use Asynclib\Producer\Publish;
class Scheduler {

    const EXCHANGE_EVENT = 'ebats_core_event';
    const EXCHANGE_TASK = 'ebats_core_task';
    const EXCHANGE_DELAY = 'ebats_core_delay';
    const QUEUE_EVENT = 'ebats_core_event';

    public function run() {
        $worker = new Consumer();
        $worker->setExchange(self::EXCHANGE_EVENT);
        $worker->setQueue(self::QUEUE_EVENT, EventManager::getEvents());
        $worker->run(function($event, $msg){
            $tasks = EventManager::getTasks($event);
            $tasks_num = EventManager::getTasksCount($event);
            $task_json = serialize($tasks);
            echo "Found {$tasks_num} task : {$task_json} \n";

            /** @var Job $task */
            foreach ($tasks as $task){
                $task->setParams($msg);
                if ($task->getDelay()){
                    $exchange_name = Scheduler::EXCHANGE_DELAY;
                    $exchange_type = ExchangeTypes::DELAY;
                }else{
                    $exchange_name = Scheduler::EXCHANGE_TASK;
                    $exchange_type = ExchangeTypes::DIRECT;
                }
                $publish = new Publish();
                $publish->setAutoClose(false);
                $publish->setExchange($exchange_name, $exchange_type);
                $publish->send($task, $task->getTopic(), $task->getDelay());
            }
        });
    }
}