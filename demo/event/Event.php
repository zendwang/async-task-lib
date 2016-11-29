<?php
require_once __DIR__.'/../autoload.php';
use Asynclib\Producer\Event;

try{
    $event = new Event('order_create');  //定义事件
    $event->setOptions(['order_id' => 'FB138020392193312']); //事件产生的参数
    $event->publish();
}catch (Exception $exc){
    echo $exc->getMessage();
}