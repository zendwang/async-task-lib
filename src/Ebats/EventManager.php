<?php
namespace Asynclib\Ebats;
use Asynclib\Core\Utils;

/**
 * EventManager
 * @author yanbo
 */
class EventManager {

    private static $events = [];

    /**
     * 注册事件
     * @param string $event 事件名称
     * @param string $task_name 任务名称
     * @param string $topic 队列标示
     * @param int $delay 延迟时间 默认0秒不延迟
     */
    public static function register($event, $task_name, $topic, $delay = 0){
        self::$events[$event][] = new Task($topic, $task_name, $delay);
    }

    /**
     * 获取事件下所有任务
     * @param $event
     * @return array|mixed
     */
    public static function getTasks($event) {
        if (isset(self::$events[$event])){
            $tasks = self::$events[$event];
            $task_num = count($tasks);
            Utils::debug("[$event] Found $task_num task.");
            return $tasks;
        }

        return array();
    }

    /**
     * 获取所有注册的事件
     * @return mixed
     */
    public static function getEvents(){
        return array_keys(self::$events);
    }
}