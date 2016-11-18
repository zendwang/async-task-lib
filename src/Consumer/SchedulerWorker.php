<?php
namespace Asynclib\Consumer;

/**
 * SchedulerWorker
 * @author yanbo
 */
class SchedulerWorker{

    private $process;
    private $queue;
    private $task_prefix = 'ebats_task_';

    public function __construct($process) {
        $this->process = $process;
    }

    public function setQueue($name) {
        $this->queue = $name;
    }

    public function run(){
        $worker = new Worker($this->process);
        $worker->setExchange(Scheduler::EXCHANGE_EVENT);
        $worker->setQueue($this->task_prefix.$this->queue, [$this->queue]);
        $worker->start();
    }
}