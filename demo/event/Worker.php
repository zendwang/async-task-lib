<?php
require_once __DIR__.'/../autoload.php';
require_once __DIR__.'/task/TaskDemoModel.php';
use Asynclib\Ebats\Worker;


$callback = function ($status, $body){

};

$worker = new Worker($callback, 3);  //支持多进程消费默认为1
$worker->setQueue('demo');  //队列名和事件的topic一一对应
$worker->run();


//Worker为系统常驻进程,建议使用pm2进行进程管理,防止异常情况下进程挂掉