<?php
require_once 'autoload.php';
use Asynclib\Consumer\EventManager;
use Asynclib\Consumer\Scheduler;

EventManager::register('order_create', 'pre_close', 'order');
EventManager::register('order_paied', 'wechat_order_paid', 'notify');

$scheduler = new Scheduler();
$scheduler->run();
