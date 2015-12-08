<?php

/**
 * https://github.com/swoole/php-cp
 *
 * @author: moxiaobai
 * @since : 2015/12/8 15:15
 */

//$obj = new pdo_connect_pool('mysql:host=192.168.1.204;dbname=gov_erp',"root","622124");
//
//$stmt = $obj->query("show tables");
//$data = $stmt->fetchAll();
//
//var_dump($data);
//
//$obj->release();

//use pool
$obj = new redis_connect_pool();
$rs = $obj->connect("192.168.1.204");
$obj->select(5);
$obj->set("test", '1111');
var_dump($obj->get("test"));
$obj->release();