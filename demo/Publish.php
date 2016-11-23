<?php
require_once 'autoload.php';

use Asynclib\Producer\Publish;
use Asynclib\Amq\ExchangeTypes;

try{
    $event = new Publish();
    $event->setExchange('demo_delay', ExchangeTypes::DELAY);
    $event->send('this is a message', '', 5);
}catch (Exception $exc){
    echo $exc->getMessage();
}