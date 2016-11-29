<?php
require_once __DIR__.'/../autoload.php';
use Asynclib\Producer\Publish;

try{
    $event = new Publish();
    $event->setExchange('demo_basic');
    $event->send('this is a basic message', 'abc');
}catch (Exception $exc){
    echo $exc->getMessage();
}