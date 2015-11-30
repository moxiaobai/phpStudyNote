<?php

/**
 * udp server
 *
 * netcat -u 127.0.0.1 9502
 *
 * @author: moxiaobai
 * @since : 2015/11/30 10:55
 */

//创建Server对象，监听 127.0.0.1:9502端口，类型为SWOOLE_SOCK_UDP
$serv = new swoole_server("127.0.0.1", 9502, SWOOLE_PROCESS, SWOOLE_SOCK_UDP);

//监听数据发送事件
$serv->on('Packet', function ($serv, $data, $clientInfo) {
    //$serv->send($fd, "Server: ".$data);
    var_dump($clientInfo);
});

//启动服务器
$serv->start();