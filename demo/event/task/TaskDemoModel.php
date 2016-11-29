<?php

/**
 * 任务模型
 * Class TaskDemoModel
 */
class TaskDemoModel {


    /**
     * 订单关闭任务
     * @param array $params 事件产生的参数
     */
    public function closeOrderTask($params){
        echo "====== " . __FUNCTION__ . " start exec ====== . \n";
        var_dump($params);

        echo "====== " . __FUNCTION__ . " exec finish ====== . \n\n";
        return true;
    }

    /**
     * 虚拟商品发货任务
     * @param array $params 事件产生的参数
     */
    public function virtualShippingTask($params){
        echo "====== " . __FUNCTION__ . " start exec ====== . \n";
        var_dump($params);

        echo "====== " . __FUNCTION__ . " exec finish ====== . \n\n";
    }

    //TODO 根据业务需求随时增加任务,开发完任务后在调度器中进行注册即可
}