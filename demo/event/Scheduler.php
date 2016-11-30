<?php
require_once __DIR__.'/../autoload.php';
use Asynclib\Ebats\EventManager;
use Asynclib\Ebats\Scheduler;

//注册事件
EventManager::register('order_create', 'closeOrder', 'demo', 10);//关闭未付款订单(延迟任务)
EventManager::register('order_paied', 'virtualShipping', 'demo'); //虚拟商品自动发货

//启动调度器
$scheduler = new Scheduler();
$scheduler->run();

//Scheduler为系统常驻进程,建议使用pm2进行进程管理,防止异常情况下进程挂掉