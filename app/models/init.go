package models

import (
	"teambition-index/context"

	"gorm.io/gorm"
)

var DB *gorm.DB

// Init 初始化model
func Init() {
	DB = context.Get().DB
	//执行迁移
	migration()
}
