<?php
namespace Asynclib\Amq;

/**
 * Exchange
 * @author yanbo
 */
trait Exchange {

    private $exchange_name;
    private $exchange_type;
    private $arguments;

    /**
     * 设置转发器
     * @param string $name 名称
     * @param string $type 类型 fanout,direct,topic 默认为direct
     */
    public function setExchange($name, $type = 'direct', $arguments = null) {
        $this->exchange_name = $name;
        $this->exchange_type = $type;
        $this->arguments = $arguments;
    }

    private function existsExchange(){
        return $this->exchange_name ? true : false;
    }

    private function getExchangeName() {
        return $this->exchange_name;
    }

    private function getExchangeType() {
        return $this->exchange_type;
    }

    public function getArguments() {
        return $this->arguments;
    }
}