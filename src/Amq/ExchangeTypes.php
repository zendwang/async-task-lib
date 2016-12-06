<?php
namespace Asynclib\Amq;

class ExchangeTypes {

    const FANOUT = 'fanout';
    const DIRECT = 'direct';
    const TOPIC  = 'topic';
    const DELAY  = 'x-delayed-message';
}