<?php
require_once 'autoload.php';
use Asynclib\Consumer\Scheduler;
$tasks = [
    'order_create' => [
        ['name' => 'pre_close', 'topic' => 'order', 'delay' => 1800]
    ],
    'order_paied' => [
        ['name' => 'wechat_order_paid', 'topic' => 'notify', 'delay' => 1800]
    ],
];
$scheduler = new Scheduler();
$scheduler->setTasks($tasks);
$scheduler->run();
