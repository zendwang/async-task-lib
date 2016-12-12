<?php
require_once __DIR__.'/../autoload.php';
use Asynclib\Core\Consumer;
use Asynclib\Amq\ExchangeTypes;
use Asynclib\Ebats\Utils;
use Asynclib\Exception\ExceptionInterface;

/**
 * 本示例演示了如何创建一个自定义调度器,开发者可以根据自身需求开发自己的任务调度器
 */
try{
    $worker = new Consumer();
    $worker->setExchange('order_fanout', ExchangeTypes::TOPIC);
    $worker->setQueue('shzf_order_paied', ['*.*.WAIT_SELLER_SEND_GOODS']);
    $worker->run(function($key, $msg){
        $order_data = json_encode($msg);
        echo " [$key] $order_data \n";

        Utils::taskCreate('demo', 'orderAsync', $msg);//创建任务,之后消息将作为参数由任务接管处理
    });
}catch (ExceptionInterface $exc){
    echo $exc->getMessage();
}
