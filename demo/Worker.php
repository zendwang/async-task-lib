<?php
require_once 'autoload.php';

use Asynclib\Consumer\Worker;
use Asynclib\Amq\ExchangeTypes;

$process = function($key, $msg){
    var_dump(microtime(true) - $msg);
    echo "test success\n";
};

$worker = new Worker($process);
$worker->setExchange('demo_delay', ExchangeTypes::DELAY, ['x-delayed-type' => ['S', 'direct']]);
$worker->setQueue('demo_delay');
$worker->start();