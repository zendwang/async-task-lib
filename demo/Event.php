<?php
require_once 'autoload.php';

use Asynclib\Producer\Event;

try{
    $event = new Event('order_create');
    $event->setOptions(['order_id' => 'FB138020392193312']);
    $event->publish();
}catch (Exception $exc){
    echo $exc->getMessage();
}