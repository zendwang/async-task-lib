<?php
namespace Asynclib\Ebats;

/**
 * Counter
 * @author yanbo
 */
class Counter {

    private $dir = '/tmp/ebats/';
    private $filename;

    public function __construct($name) {
        $this->filename = "{$this->dir}$name";
        if (!file_exists($this->filename)){
            touch($this->filename);
        }
    }

    public function get(){
        $fp = fopen($this->filename, 'r+');
        $content = fgets($fp, 50);
        fclose($fp);
        return intval($content);
    }

    public function incr(){
        $fp = fopen($this->filename, 'r+');
        $new_num = $this->get() + 1;
        fwrite($fp, $new_num);
        fclose($fp);
    }

    public function clear(){
        unlink($this->filename);
    }
}