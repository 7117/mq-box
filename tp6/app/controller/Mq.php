<?php
// +----------------------------------------------------------------------
// | najing [ 通用后台管理系统 ]
// +----------------------------------------------------------------------
// | Copyright (c) 2020 http://www.najingquan.com All rights reserved.
// +----------------------------------------------------------------------
// | Author: 救火队队长
// +----------------------------------------------------------------------

namespace app\controller;

use app\BaseController;
use PhpAmqpLib\Connection\AMQPStreamConnection;
use PhpAmqpLib\Exchange\AMQPExchangeType;
use PhpAmqpLib\Message\AMQPMessage;
use PhpAmqpLib\Wire\AMQPTable;

class Mq extends BaseController
{
    /**
     * 功能描述: 生产者，负责发送消息
     * @author 救火队队长
     * @return string
     */
    public function send()
    {
        //队列名  消息队列载体，每个消息都会被投入到一个或多个队列。
        $queue = 'hello';

        //建立连接
        $connection = new AMQPStreamConnection('192.168.106.199', 15672, 'zq', '123456', '/');
        //获取信道
        $channel = $connection->channel();

        //声明创建队列
        $channel->queue_declare($queue, false, false, false, false);

        for ($i=0; $i < 5; ++$i) {
            sleep(1);//休眠1秒
            //消息内容
            $messageBody = "Hello,Zq Now Time:".date("h:i:s");
            //将我们需要的消息标记为持久化 - 通过设置AMQPMessage的参数delivery_mode = AMQPMessage::DELIVERY_MODE_PERSISTENT
            $message = new AMQPMessage($messageBody, array('content_type' => 'text/plain', 'delivery_mode' => AMQPMessage::DELIVERY_MODE_PERSISTENT));
            //发送消息
            $channel->basic_publish($message, '', '');
            echo "nihao,Send Message:". $i."\n";
        }

        //关闭信道
        $channel->close();
        //关闭连接
        $connection->close();
        return 'OVER Send Success';
    }
}
