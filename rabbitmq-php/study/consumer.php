<?php
//过程：连接rmq  设置通道  设置交换机  设置队列
//声明路由键 交换机的名称
$exName = 'exchange_key';
$routingKey = 'routing_key';
$queueName = 'queuename';
$config = [
    'host' => '192.168.146.130',
    'vhost' => '/',
    'port' => 5672,
    'login' => 'zq',
    'password' => '123456'
];

//连接rmq
$conn = new AMQPConnection($config);
$conn->connect();
//新建通道
$channel = new AMQPChannel($conn);


//交换机设置:把路由
$ex = new AMQPExchange($channel);
//设置交换机名称
$ex->setName($exName);
// 声明一下交换机类型
// direct处理路由键   根据路由键分发
// fanout不处理路由键  类似于广播
// topic肯定规则进行分发   类似于模糊搜索
// headers根据属性进行分发  匹配键值对的  all   math
$ex->setType(AMQP_EX_TYPE_DIRECT);
// 设置持久化
$ex->setFlags(AMQP_DURABLE);

//队列设置
$q = new AMQPQueue($channel);
$q->setName($queueName);
$q->setFlags(AMQP_DURABLE);
$q->declareQueue();
//队列在中间要绑定交换机与路由键
$q->bind($exName, $routingKey);
// 接收消息并且进行处理的回调的方法
$q->consume("receive");
function receive($envelope, $queue)
{
    //休眠两秒
    sleep(2);
    echo $envelope->getBody() . PHP_EOL;
    //显示确认  队列收到消息后 进行确认后  会删除消息
    $queue->ack($envelope->getDeliveryTag());
}