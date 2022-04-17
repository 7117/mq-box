<?php

use PhpAmqpLib\Connection\AMQPStreamConnection;
use PhpAmqpLib\Exchange\AMQPExchangeType;
use PhpAmqpLib\Message\AMQPMessage;

include './config.php';

$connection = new AMQPStreamConnection($host, $port, $user, $password, $vhost);
$channel = $connection->channel();

//保存订单消息到数据库；是否将【成功的消息】推送到消息队列


//ack因素1：开启确认模式
$channel->confirm_select();
//ack因素2：进行监听成功与失败
$channel->wait_for_pending_acks();

//推送成功会触发此个函数：设置ack内容与动作
//ack因素3：用AMQPMessage作为第一个参数设置一个处理程序，该处理程序调用由服务器确认的任何消息。
$channel->set_ack_handler(
    function (AMQPMessage $message) {
        echo "success" . $message->body . PHP_EOL;
    }
);

//ack因素3：推送失败会触发此个函数：设置ack内容与动作
$channel->set_nack_handler(
    //这里可以用来修改状态啥的  标记发送成功
    function (AMQPMessage $message) {
        echo "fail" . $message->body . PHP_EOL;
    }
);

$channel->exchange_declare($exchange, AMQPExchangeType::FANOUT, false, false, true);

//此处是单个消息
$i = 1;
$msg = new AMQPMessage($i, ['content_type' => 'text/plain']);
$channel->basic_publish($msg, $exchange);
$channel->close();


//此处是多个消息
// while ($i <= 11) {
//     $msg = new AMQPMessage($i, ['content_type' => 'text/plain']);
//     $channel->basic_publish($msg, $exchange);
// }
//
// $channel->close();