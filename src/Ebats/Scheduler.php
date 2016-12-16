<?php
namespace Asynclib\Ebats;

use Asynclib\Amq\ExchangeTypes;
use Asynclib\Core\Consumer;
use Asynclib\Core\Publish;
use Asynclib\Core\Logs;
class Scheduler {

    const EXCHANGE_EVENT = 'ebats.core.event';
    const EXCHANGE_TASK = 'ebats.task.ready';
    const EXCHANGE_DELAY = 'ebats.task.delay';
    const QUEUE_EVENT = 'ebats.events';

    public function run() {
        $events = EventManager::getEvents();
        Logs::info('Loaded event '. json_encode($events).'.');
        Logs::info('Scheduler start.');
        $consumer = new Consumer();
        $consumer->setExchange(self::EXCHANGE_EVENT);
        $consumer->setQueue(self::QUEUE_EVENT, $events);
        $consumer->run(function($event, $msg){
            Logs::info("The event $event coming.");
            $tasks = EventManager::getTasks($event);
            /** @var Task $task */
            foreach ($tasks as $task){
                $task->setParams($msg);

                $publish = new Publish();
                $publish->setAutoClose(false);
                $publish->setExchange($task->getDelay() ? Scheduler::EXCHANGE_DELAY : Scheduler::EXCHANGE_TASK);
                $publish->send($task, $task->getTopic(), $task->getDelay());
                Logs::info("[{$task->getTopic()}] {$task->getName()} published, delay: {$task->getDelay()}");
            }
        });
    }
}