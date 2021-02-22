package models

type BaseModel struct {
	ID        uint      `gorm:"primarykey"`
	CreatedAt TimeField `gorm:"autoCreateTime"`
	UpdatedAt TimeField `gorm:"autoUpdateTime"`
}

type TimeField int64
