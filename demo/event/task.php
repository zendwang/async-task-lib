<?php
require_once __DIR__.'/../autoload.php';
use Asynclib\Ebats\Scheduler;
use Asynclib\Ebats\Task;
use Asynclib\Core\Publish;

try{
    $task = new Task('demo', 'updateAbc', 5);
    $task->setParams(['acc' => 123]);

    $publish = new Publish();
    $publish->setExchange(Scheduler::EXCHANGE_DELAY);
    $publish->send($task, $task->getTopic(), $task->getDelay());
}catch (Exception $exc){
    echo $exc->getMessage();
}