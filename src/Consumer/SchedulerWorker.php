<?php
namespace Asynclib\Consumer;

/**
 * SchedulerWorker
 * @author yanbo
 */
class SchedulerWorker{

    private $process;
    private $process_num;
    private $queue;
    private $task_prefix = 'ebats_task_';

    public function __construct($process, $process_num) {
        $this->process = $process;
        $this->process_num = $process_num;
    }

    public function setQueue($name) {
        $this->queue = $name;
    }

    public function run(){
        $worker = new Worker($this->process, $this->process_num);
        $worker->setExchange(Scheduler::EXCHANGE_TASK);
        $worker->setQueue($this->task_prefix.$this->queue, [$this->queue]);
        $worker->start();
    }
}