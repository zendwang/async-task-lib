<?php

/**
 * 任务模型
 * Class TaskDemoModel
 */
use Asynclib\Exception\TaskException;
use Asynclib\Exception\RetryException;
class TaskDemoModel {


    /**
     * 订单关闭任务
     * @param array $params 事件产生的参数
     */
    public function closeOrderTask($params){
//        var_dump($params);

        return false;
    }

    /**
     * 虚拟商品发货任务
     * @param array $params 事件产生的参数
     */
    public function virtualShippingTask($params){
//        var_dump($params);

        //根据业务需求抛出不同异常,TaskException为普通异常,RetryException为需要重试的异常
//        throw new TaskException('获取数据失败');
        throw new RetryException('获取数据失败233'); //默认为系统统一配置 
//        throw new RetryException('获取数据失败123', 3, 5);  //错误信息,重试次数,重试间隔

    }

    public function orderAsyncTask($params){
        var_dump($params);
    }

    //TODO 根据业务需求随时增加任务,开发完任务后在调度器中进行注册即可
}