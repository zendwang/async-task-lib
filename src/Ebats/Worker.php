<?php
namespace Asynclib\Ebats;

use Asynclib\Amq\ExchangeTypes;
use Asynclib\Core\Consumer;
use Asynclib\Core\Logs;
use Asynclib\Core\Publish;
use Asynclib\Exception\RetryException;
use Asynclib\Exception\TaskException;

class Worker{

    private $callback;
    private $process_num;
    private $queue;
    private $task_prefix = ['ebats_ntask_', 'ebats_dtask_'];
    private $retry_interval = [5, 300, 600, 3600, 7800];  //5s 5min 10min 1h 2h

    public function __construct($callback, $process_num = 1) {
        $this->callback = $callback;
        $this->process_num = $process_num;
    }

    public function setQueue($name) {
        $this->queue = $name;
    }

    /**
     * @param string $key
     * @param Task $task
     * @return mixed
     */
    public function exec($topic, $task) {
        $topic = ucfirst($topic);
        $params = json_encode($task->getParams());
        Logs::info("[$topic]{$task->getName()} start exec with the params - $params.");
        $class = "Task{$topic}Model";
        $action = "{$task->getName()}Task";
        if (!class_exists($class)){
            die("[$topic] $class is not exists. \n");
        }

        $timebegin = microtime(true);//标记任务开始执行时间
        $model = new $class();
        if (!method_exists($model, $action)){
            die("[$topic] $action is not exists. \n");
        }
        $model->$action($task->getParams());
        $endtime = microtime(true); //标记任务执行完成时间
        $timeuse = ($endtime - $timebegin) * 1000; //计算任务执行用时
        Logs::info("[$topic]{$task->getName()} exec finished, timeuse - {$timeuse}ms.");
        return $timeuse;
    }

    /**
     * 重试机制
     * @param string $key
     * @param Task $task
     * @param int $retry 重试次数
     * @param int $interval 重试间隔
     */
    private function retry($key, $task, $retry, $interval){
        $counter = new Counter($task->getId());
        $fail_times = $counter->get() + 1;
        $retry_times = $retry ? : count($this->retry_interval);   //重试次数
        $delay_time = $retry ? $interval : $this->retry_interval[$fail_times]; //重试间隔
        if ($fail_times > $retry){
            $counter->clear();
            Logs::info("[$key]{$task->getName()} exec failed, retry end.");
            return;
        }

        Logs::info("[$key]{$task->getName()} exec failed, after $delay_time seconds retry[$fail_times/$retry_times].");
        $publish = new Publish();
        $publish->setAutoClose(false);
        $publish->setExchange(Scheduler::EXCHANGE_DELAY, ExchangeTypes::DELAY);
        $publish->send($task, $key, $delay_time);
        $counter->incr();
    }

    public function process(\swoole_process $swoole_process){
        $index = $swoole_process->read();
        if ($index){
            $exchange_name = Scheduler::EXCHANGE_DELAY;
            $exchange_type = ExchangeTypes::DELAY;
        }else{
            $exchange_name = Scheduler::EXCHANGE_TASK;
            $exchange_type = ExchangeTypes::DIRECT;
        }

        $worker = new Consumer();
        $worker->setExchange($exchange_name, $exchange_type);
        $worker->setQueue($this->task_prefix[$index].$this->queue, [$this->queue]);
        $worker->run(function($key, $task){
            /** @var Task $task */
            $timeuse = -1;
            $message = 'ok.';
            try{
                $timeuse = $this->exec($key, $task);
            }catch (TaskException $exc){
                $message = $exc->getMessage();
                Logs::error("[$key]{$task->getName()} exec failed  - $message");
            }catch (RetryException $exc){
                $message = $exc->getMessage();
                Logs::error("[$key]{$task->getName()} exec failed  - $message");
                $this->retry($key, $task, $exc->getRetry(), $exc->getInterval());
            }

            //将执行情况回调给上层开发者
            call_user_func($this->callback, $task->getTopic(), $task->getName(), $task->getName(), $task->getParams(), $timeuse, $message);
        });
    }

    public function run(){
        Logs::info("Worker start init, process_num is {$this->process_num}.");
        for($i = 0; $i < $this->process_num; $i++){
            for ($n = 0; $n < 2; $n++){
                $process = new \swoole_process([$this, 'process']);
                $process->write($n);
                $process->start();
            }
        }
        Logs::info("Worker init finished.\n");
        \swoole_process::wait();
    }
}