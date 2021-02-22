package models

import (
	"strings"
	"teambition-index/pkg/hashid"
	"teambition-index/pkg/util"
	"time"

	"gorm.io/gorm"
)

type Share struct {
	BaseModel
	UserID          string    // 用户ID
	NodeID          string    // 原始资源ID
	Name            string    `gorm:"index:source"` // 用于搜索的字段
	Views           int       // 浏览数
	Downloads       int       // 下载数
	IsDir           bool      // 是否文件夹
	RemainDownloads int       // 剩余下载配额，负值标识无限制
	PreviewEnabled  bool      // 是否允许直接预览
	Password        string    // 分享密码，空值为非加密分享
	Expires         TimeField // 过期时间，空值表示无过期时间
}

// Create 创建分享
func (share *Share) Create() (uint, error) {
	if err := DB.Create(share).Error; err != nil {
		util.Log().Warning("无法插入数据库记录, %s", err)
		return 0, err
	}
	return share.ID, nil
}

// GetShareByHashID 根据HashID查找分享
func GetShareByHashID(hashID string) *Share {
	id, err := hashid.DecodeHashID(hashID, hashid.ShareID)
	if err != nil {
		return nil
	}
	var share Share
	result := DB.First(&share, id)
	if result.Error != nil {
		return nil
	}

	return &share
}

// Viewed 增加访问次数
func (share *Share) Viewed() {
	share.Views++
	DB.Model(share).UpdateColumn("views", gorm.Expr("views + ?", 1))
}

// Downloaded 增加下载次数
func (share *Share) Downloaded() {
	share.Downloads++
	if share.RemainDownloads > 0 {
		share.RemainDownloads--
	}
	DB.Model(share).Updates(map[string]interface{}{
		"downloads":        share.Downloads,
		"remain_downloads": share.RemainDownloads,
	})
}

// Update 更新分享属性
func (share *Share) Update(props map[string]interface{}) error {
	return DB.Model(share).Updates(props).Error
}

// Delete 删除分享
func (share *Share) Delete() error {
	return DB.Model(share).Delete(share).Error
}

// ListShares 列出UID下的分享
func ListShares(uid string, page, pageSize int, order string, publicOnly bool) ([]Share, int64) {
	var (
		shares []Share
		total  int64
	)
	dbChain := DB
	dbChain = dbChain.Where("user_id = ?", uid)
	if publicOnly {
		dbChain = dbChain.Where("password = ?", "")
	}

	// 计算总数用于分页
	dbChain.Model(&Share{}).Count(&total)

	// 查询记录
	dbChain.Limit(pageSize).Offset((page - 1) * pageSize).Order(order).Find(&shares)
	return shares, total
}

// SearchShares 根据关键字搜索分享
func SearchShares(page, pageSize int, order, keywords string) ([]Share, int64) {
	var (
		shares []Share
		total  int64
	)

	keywordList := strings.Split(keywords, " ")
	availableList := make([]string, 0, len(keywordList))
	for i := 0; i < len(keywordList); i++ {
		if len(keywordList[i]) > 0 {
			availableList = append(availableList, keywordList[i])
		}
	}
	if len(availableList) == 0 {
		return shares, 0
	}

	dbChain := DB
	dbChain = dbChain.Where("password = ? and remain_downloads <> 0 and (expires is NULL or expires > ?) and source_name like ?", "", time.Now(), "%"+strings.Join(availableList, "%")+"%")

	// 计算总数用于分页
	dbChain.Model(&Share{}).Count(&total)

	// 查询记录
	dbChain.Limit(pageSize).Offset((page - 1) * pageSize).Order(order).Find(&shares)
	return shares, total
}

// DeleteShareBySourceIDs 根据原始资源类型和ID删除文件
func DeleteShareBySourceIDs(sources []uint, isDir bool) error {
	return DB.Where("source_id in (?) and is_dir = ?", sources, isDir).Delete(&Share{}).Error
}
