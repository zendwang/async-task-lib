<?php
namespace Asynclib\Consumer;

/**
 * SchedulerWorker
 * @author yanbo
 */
use Asynclib\Amq\ExchangeTypes;
use Asynclib\Producer\Publish;
class Worker{

    private $callback;
    private $process_num;
    private $queue;
    private $task_prefix = ['ebats_ntask_', 'ebats_dtask_'];
    private $retry_interval = [5, 300, 600, 3600, 7800];  //5s 5min 10min 1h 2h

    public function __construct($callback, $process_num) {
        $this->callback = $callback;
        $this->process_num = $process_num;
    }

    public function setQueue($name) {
        $this->queue = $name;
    }

    /**
     * @param string $key
     * @param Job $task
     * @return mixed
     */
    public function exec($key, $task) {
        $timebegin = microtime(true);//标记任务开始执行时间
        $topic = ucfirst($key);
        $class = "Task{$topic}Model";
        $action = "{$task->getName()}Task";
        if (!class_exists($class)){
            die("[$topic] $class is not exists. \n");
        }

        $model = new $class();
        if (!method_exists($model, $action)){
            die("[$topic] $action is not exists. \n");
        }
        $result = $model->$action($task->getParams());

        $endtime = microtime(true); //标记任务执行完成时间
        $timeuse = ($endtime - $timebegin) * 1000; //计算任务执行用时
        call_user_func($this->callback, $action, $timeuse);
        return $result;
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

        /**
         * @param string $key
         * @param Job $task
         */
        $process = function($key, $task){
            if (!$this->exec($key, $task)){
                //重试机制
                $counter = new Counter($task->getId());
                $fail_times = $counter->get();
                echo "exec the $fail_times times fail. join it into the delay queue. \n";
                if ($fail_times < count($this->retry_interval)){
                    $publish = new Publish();
                    $publish->setAutoClose(false);
                    $publish->setExchange(Scheduler::EXCHANGE_DELAY, ExchangeTypes::DELAY);
                    $publish->send($task, $key, $this->retry_interval[$fail_times]);
                    $counter->incr();
                }else{
                    $counter->clear();
                }
            }
        };
        $worker = new Consumer();
        $worker->setExchange($exchange_name, $exchange_type);
        $worker->setQueue($this->task_prefix[$index].$this->queue, [$this->queue]);
        $worker->run($process);
    }

    public function run(){
        for($i = 0; $i < $this->process_num; $i++){
            for ($n = 0; $n < 2; $n++){
                $process = new \swoole_process([$this, 'process']);
                $process->write($n);
                $process->start();
            }
        }
        \swoole_process::wait();
    }
}