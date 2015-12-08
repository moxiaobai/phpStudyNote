<?php

/**
 * 定时器
 *
 * @author: moxiaobai
 * @since : 2015/11/30 11:28
 */

//每隔2000ms触发一次
swoole_timer_tick(2000, function ($timer_id) {
    echo "tick-2000ms\n";
});