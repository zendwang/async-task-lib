<?php
namespace Asynclib\Exception;

class RetryException extends \RuntimeException implements ExceptionInterface{

    private $retry;
    private $interval;

    public function __construct($message, $retry = 3, $interval = 300) {
        parent::__construct($message);
        $this->retry = $retry;
        $this->interval = $interval;
    }

    public function getRetry() {
        return $this->retry;
    }

    public function getInterval() {
        return $this->interval;
    }
}