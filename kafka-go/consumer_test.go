package main

import (
	"github.com/zngw/kafka"
	"github.com/zngw/log"
	"os/signal"
	"runtime"
	"syscall"
)

func main() {
	// 初始化日志
	err := log.Init(nil)
	if err != nil {
		panic(err)
	}

	// 初始化消费者
	err = kafka.InitConsumer("192.168.1.29:9092")
	if err != nil {
		panic(err)
	}

	// 监听
	go func() {
		err = kafka.LoopConsumer("Test", TopicCallBack)
		if err != nil {
			panic(err)
		}
	}()

	signal.Ignore(syscall.SIGHUP)
	runtime.Goexit()
}

func TopicCallBack(data []byte) {
	log.Trace("kafka", "Test:"+string(data))
}
