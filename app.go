package main

import (
	"context"
	"net/http"
	"os"
	"os/signal"
	"syscall"
	"teambition-index/bootstrap"
	global "teambition-index/context"
	"teambition-index/pkg/util"
	"teambition-index/routes"
	"time"
)

func init() {
	bootstrap.Init()
}
func main() {
	app := routes.InitRouter()

	addr := global.Get().ServerAddress

	srv := &http.Server{
		Addr:    addr,
		Handler: app,
	}

	// Initializing the server in a goroutine so that
	// it won't block the graceful shutdown handling below
	util.Log().Info("开始监听：%s", addr)
	go func() {
		if err := srv.ListenAndServe(); err != nil && err != http.ErrServerClosed {
			util.Log().Info("服务监听地址：%s", addr)
		}
	}()

	// Wait for interrupt signal to gracefully shutdown the server with
	// a timeout of 5 seconds.
	quit := make(chan os.Signal)
	// kill (no param) default send syscall.SIGTERM
	// kill -2 is syscall.SIGINT
	// kill -9 is syscall.SIGKILL but can't be catch, so don't need add it
	signal.Notify(quit, syscall.SIGINT, syscall.SIGTERM)
	<-quit
	util.Log().Warning("关闭服务中...")

	// The context is used to inform the server it has 5 seconds to finish
	// the request it is currently handling
	ctx, cancel := context.WithTimeout(context.Background(), 5*time.Second)
	defer cancel()
	if err := srv.Shutdown(ctx); err != nil {
		util.Log().Warning("服务强制退出：%s", err)
	}

	util.Log().Warning("服务已退出")
}
