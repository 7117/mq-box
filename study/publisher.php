<?php
//过程：连接rmq  设置交换机  进行发送教习
//声明路由键 交换机的名称
$routingKey = 'routing_key';
$exName = 'exchange_key';
$config = [
    'host' => '192.168.146.130',
    'vhost' => '/',
    'port' => 5672,
    'login' => 'zq',
    'password' => '123456',
    'heartbeat' => 30
];
//连接
$conn = new AMQPConnection($config);
$conn->connect();
$channel = new AMQPChannel($conn);
$ex = new AMQPExchange($channel);

//设置交换机 名称  类型  持久化
$ex->setName($exName);
$ex->setType(AMQP_EX_TYPE_DIRECT);
$ex->setFlags(AMQP_DURABLE);
$ex->declareExchange();

// 创建10个消息
for ($i = 1; $i <= 5; $i++) {
    // 消息内容
    $msg = [
        'data' => 'msg_' . $i,
        'hello' => 'world',
    ];
    echo "send msg:" . $ex->publish(json_encode($msg), $routingKey, AMQP_DURABLE, ['delivery_mode' => 2]) . PHP_EOL;
}
