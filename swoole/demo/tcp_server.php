<?php

/**
 * tcp server
 *
 * @author: moxiaobai
 * @since : 2015/11/30 10:34
 */

//创建Server对象，监听 0:0:0:0:9501端口
$serv = new swoole_server("0.0.0.0", 9501);

//设置参数
$serv->set(array(
    'reactor_num'     => 2,
    'worker_num'      => 4,
    'task_worker_num' => 4,
    'log_file'        => __DIR__ . '/swoole.log',
    'debug_mode'      => 1 ,
    'daemonize'       => 1,
    'heartbeat_check_interval' => 5,
    'heartbeat_idle_time' => 10,
));

//启动服务
$serv->on('start', function($serv) {
    echo "Server Start: " . date('Y-m-d H:i:s') . PHP_EOL;
    echo "Master Pid:" . $serv->master_pid . PHP_EOL;
    echo "Manage Pid:" . $serv->manager_pid . PHP_EOL;
    swoole_set_process_name('tcpServer');
});

//监听worker进程
$serv->on('WorkerStart', function($serv, $worker_id) {
//    if($worker_id >= $serv->setting['worker_num']) {
//        swoole_set_process_name("php task worker");
//    } else {
//        swoole_set_process_name("php event worker");
//    }

    echo "Worker Pid:" . $serv->worker_pid . PHP_EOL;
});

//监听连接进入事件
$serv->on('connect', function ($serv, $fd) {
    $fdinfo = $serv->connection_info($fd);
    var_dump($fdinfo);
});

//监听数据发送事件
$serv->on('receive', function ($serv, $fd, $from_id, $data) {
    //$serv->send($fd, "Server: ".$data);

    //发送广播
    $start_fd = 0;
    while(true)
    {
        $conn_list = $serv->connection_list($start_fd, 10);
        if($conn_list===false or count($conn_list) === 0)
        {
            echo "finish\n";
            break;
        }
        $start_fd = end($conn_list);
        var_dump($conn_list);
        foreach($conn_list as $fd)
        {
            $serv->send($fd, "broadcast");
        }
    }

    //投递任务
    $serv->task($data);
});

//处理异步任务
$serv->on('task', function ($serv, $task_id, $from_id, $data) {
    echo "New AsyncTask[id=$task_id]".PHP_EOL;
    //返回任务执行的结果
    $serv->finish("$data -> OK");
});

//处理异步任务的结果
$serv->on('finish', function ($serv, $task_id, $data) {
    echo "AsyncTask[$task_id] Finish: $data".PHP_EOL;
});

//监听连接关闭事件
$serv->on('close', function ($serv, $fd, $from_id) {
    echo "Client: Close" . PHP_EOL;
});

//需要使用kill -15来发送SIGTREM信号到主进程才能按照正常的流程终止
$serv->on('Shutdown', function(){
    echo '服务器关闭: ' . date('Y-m-d H:i:s') . PHP_EOL;
});

//启动服务器
$serv->start();