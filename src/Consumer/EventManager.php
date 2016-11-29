<?php
namespace Asynclib\Consumer;

/**
 * EventManager
 * @author yanbo
 */
class EventManager {

    private static $events = [];

    /**
     * 注册事件
     * @param string $event 事件名称
     * @param string $task 任务名称
     * @param string $topic 队列标示
     * @param int $delay 延迟时间 默认0秒不延迟
     */
    public static function register($event, $task, $topic, $delay = 0){
        self::$events[$event][] = new Job($topic, $task, $delay);
    }

    /**
     * 获取事件下所有任务
     * @param $event
     * @return array|mixed
     */
    public static function getTasks($event) {
        return isset(self::$events[$event]) ? self::$events[$event] : array();
    }

    public static function getTasksCount($event){
        return count(self::$events[$event]);
    }

    /**
     * 获取所有注册的事件
     * @return mixed
     */
    public static function getEvents(){
        return array_keys(self::$events);
    }
}