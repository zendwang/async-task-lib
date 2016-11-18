<?php
namespace Asynclib\Consumer;

/**
 * EventManager
 * @author yanbo
 */
class EventManager {

    private static $events = [];

    public static function register($event, $task, $topic, $delay = 0){
        self::$events[$event][] = ['name' => $task, 'topic' => $topic, 'delay' => $delay];
    }

    public static function getTasks($event) {
        return isset(self::$events[$event]) ? self::$events[$event] : array();
    }

    public static function getEvents(){
        return array_keys(self::$events);
    }
}