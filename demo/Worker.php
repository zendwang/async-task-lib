<?php
require_once 'autoload.php';

use Asynclib\Consumer\Worker;

$process = function($key, $msg){
    var_dump($key, $msg);
    echo "test success\n";
};

$worker = new Worker($process);
$worker->setExchange('ebats_core_task');
$worker->setQueue('ebats_task_order', ['order']);
$worker->start();