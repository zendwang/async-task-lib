<?php
namespace Asynclib\Ebats;
use Asynclib\Core\Publish;

class Utils {

    /**
     * 创建一个任务并发送给任务处理交换器
     */
    public static function taskCreate($topic, $name, $params) {
        $task = new Task($topic, $name);
        $task->setParams($params);

        $publish = new Publish();
        $publish->setAutoClose(false);
        $publish->setExchange(Scheduler::EXCHANGE_TASK);
        $publish->send($task, $task->getTopic(), $task->getDelay());
    }
}