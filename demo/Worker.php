<?php
require_once 'autoload.php';

use Asynclib\Consumer\Worker;

$process = function($key, $msg){
    var_dump($key, $msg);
    echo "test success\n";
};

$worker = new Worker($process);
$worker->setExchage('demo_test');
$worker->setQueue('demo_test', ['abc']);
$worker->start();