package models

import (
	"fmt"
	"teambition-index/pkg/conf"
	"teambition-index/pkg/util"
)

// needMigration 是否需要迁移
func needMigration() bool {

	var setting Setting
	return DB.Where("name = ?", fmt.Sprintf("db_version_%s", conf.RequiredDBVersion)).First(&setting).Error != nil
}

// migration 执行迁移
func migration() {
	// 确认是否需要执行迁移
	if !needMigration() {
		util.Log().Info("数据库版本匹配，跳过数据库迁移")
		return
	}

	util.Log().Info("开始进行数据库初始化...")

	// 自动迁移模式
	if conf.DatabaseConfig.Type == "mysql" {
		DB = DB.Set("gorm:table_options", "ENGINE=InnoDB")
	}

	err := DB.AutoMigrate(&Share{}, &Setting{})
	if err != nil {
		util.Log().Info("数据库迁移错误，%s", err.Error())
		return
	}

	// 向设置数据表添加初始设置
	addDefaultSettings()

	util.Log().Info("数据库初始化结束.")
}

// addDefaultSettings 添加默认设置
func addDefaultSettings() {
	defaultSettings := []Setting{
		{Name: fmt.Sprintf("db_version_%s", conf.RequiredDBVersion), Value: `installed`, Type: "version"},
	}

	for _, value := range defaultSettings {
		DB.Where(Setting{Name: value.Name}).Create(&value)
	}
}
