package crontab

import (
	"teambition-index/pkg/cache"
	"teambition-index/pkg/util"
)

func garbageCollect() {

	// 清理过期的内置内存缓存
	if store, ok := cache.Store.(*cache.MemoStore); ok {
		collectCache(store)
	}

	util.Log().Info("定时任务 [cron_garbage_collect] 执行完毕")
}

func collectCache(store *cache.MemoStore) {
	util.Log().Debug("清理内存缓存")
	store.GarbageCollect()
}
