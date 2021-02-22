package crontab

import (
	"teambition-index/pkg/util"

	"github.com/robfig/cron/v3"
)

// Cron 定时任务
var Cron *cron.Cron

// Reload 重新启动定时任务
func Reload() {
	if Cron != nil {
		Cron.Stop()
	}
	Init()
}

// Init 初始化定时任务
func Init() {
	util.Log().Info("初始化定时任务...")
	Cron := cron.New()
	if _, err := Cron.AddFunc("@hourly", garbageCollect); err != nil {
		util.Log().Warning("无法启动定时任务 [%s] , %s", "garbageCollect", err)
	}

	Cron.Start()
}
