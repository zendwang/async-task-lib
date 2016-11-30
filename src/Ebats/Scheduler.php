<?php
namespace Asynclib\Ebats;

/**
 * Scheduler
 * @author yanbo
 */
use Asynclib\Amq\ExchangeTypes;
use Asynclib\Core\Consumer;
use Asynclib\Core\Publish;
use Asynclib\Core\Utils;

class Scheduler {

    const EXCHANGE_EVENT = 'ebats_core_event';
    const EXCHANGE_TASK = 'ebats_core_ntask';
    const EXCHANGE_DELAY = 'ebats_core_dtask';
    const QUEUE_EVENT = 'ebats_core_event';

    public function run() {
        $events = EventManager::getEvents();
        Utils::debug('Loaded event '. json_encode($events).'.');
        Utils::debug('Scheduler start.');
        $consumer = new Consumer();
        $consumer->setExchange(self::EXCHANGE_EVENT);
        $consumer->setQueue(self::QUEUE_EVENT, $events);
        $consumer->run(function($event, $msg){
            Utils::debug("The event $event coming.");
            $tasks = EventManager::getTasks($event);
            /** @var Task $task */
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
                Utils::debug("[{$task->getTopic()}] {$task->getName()} published, delay: {$task->getDelay()}");
            }
        });
    }
}