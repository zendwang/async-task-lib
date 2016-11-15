<?php
require_once 'autoload.php';

use Asynclib\Producer\Publish;

try{
    $worker = new Publish();
    $worker->setExchage('demo_test');
    $worker->send('1111', 'abc');
}catch (Exception $exc){
    echo $exc->getMessage();
}