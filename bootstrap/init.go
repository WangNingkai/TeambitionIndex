package bootstrap

import (
	"teambition-index/app/models"
	"teambition-index/context"
	"teambition-index/pkg/cache"
	"teambition-index/pkg/conf"
	"teambition-index/pkg/crontab"
	"teambition-index/pkg/util"

	"github.com/gin-gonic/gin"
)

// Init 初始化启动
func Init() {
	InitApplication()

	context.InitContext()
	// Debug 关闭时，切换为生产模式
	if !conf.SystemConfig.Debug {
		gin.SetMode(gin.ReleaseMode)
	}
	cache.Init()
	models.Init()
	crontab.Init()
	InitStatic()
	util.Log().Info("初始化完成.")
}
