package main

import (
    "strings"
    "github.com/Shopify/sarama"
    "github.com/zngw/log"
)

var producer sarama.AsyncProducer

// 初始化生产者
func InitProducer(hosts string) {
    config := sarama.NewConfig()
    client, err := sarama.NewClient(strings.Split(hosts, ","), config)
    if err != nil {
        log.Error("unable to create kafka client: ", err)
    }
    producer, err = sarama.NewAsyncProducerFromClient(client)
    if err != nil {
        log.Error(err)
    }
}

// 发送消息
func Send(topic, data string) {
    producer.Input() <- &sarama.ProducerMessage{Topic: topic, Key: nil, Value: sarama.StringEncoder(data)}
    log.Trace("kafka", "Produced message: ["+ data+"]")
}

func Close() {
    if producer != nil {
        producer.Close()
    }
}
————————————————
版权声明：本文为「过客」的原创文章，遵循 CC 4.0 BY-SA 版权协议，转载请附上原文出处链接及本声明。
原文链接：https://zengwu.com.cn/p/c6a5e268.html