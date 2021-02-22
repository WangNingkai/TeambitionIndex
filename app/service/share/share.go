package share

import (
	"fmt"
	"teambition-index/pkg/cache"
	"teambition-index/pkg/hashid"
	"teambition-index/pkg/serializer"
	"teambition-index/pkg/teambition"

	model "teambition-index/app/models"

	"github.com/gin-gonic/gin"
	"github.com/golang-module/carbon"
	"github.com/unknwon/com"
)

type ShareListService struct {
	Page    int `form:"page" binding:"required"`
	PerPage int `form:"perPage" binding:"required"`
}

type ShareViewService struct {
	Hash string `uri:"hash" binding:"required"`
}

type ShareCreateService struct {
	Name   string `json:"name" binding:"required"`
	NodeID string `json:"nodeId" binding:"required"`
}

type ShareDeleteService struct {
	ID uint `uri:"id" binding:"required"`
}

type ShareListResponse struct {
	CurrentPage int         `json:"currentPage"`
	PerPage     int         `json:"perPage"`
	TotalCount  int         `json:"totalCount"`
	TotalPage   int         `json:"totalPage"`
	List        []ShareItem `json:"list"`
}
type ShareItem struct {
	ID        uint   `json:"id"`
	UserID    string `json:"user_id"`
	NodeID    string `json:"node_id"`
	Name      string `json:"name"`
	CreatedAt string `json:"created_at"`
	Hash      string `json:"hash"`
}

// 分享列表
func (service *ShareListService) List(c *gin.Context, user *teambition.UserInfo) serializer.Response {
	// 列出分享
	shares, total := model.ListShares(user.User.ID, int(service.Page), int(service.PerPage), "id DESC", false)
	var totalPage int
	if service.PerPage < 1 {
		if total > 0 {
			totalPage = 1
		} else {
			totalPage = 0
		}

	} else {
		totalPage = (int(total) + service.PerPage - 1) / service.PerPage
	}
	list := make([]ShareItem, 0, len(shares))

	for _, share := range shares {
		item := ShareItem{
			ID:        share.ID,
			UserID:    share.UserID,
			NodeID:    share.NodeID,
			Name:      share.Name,
			CreatedAt: carbon.Parse(com.ToStr(share.CreatedAt)).ToDateTimeString(),
			Hash:      hashid.HashID(share.ID, hashid.ShareID),
		}
		list = append(list, item)
	}

	res := ShareListResponse{
		CurrentPage: int(service.Page),
		PerPage:     int(service.PerPage),
		TotalCount:  int(total),
		TotalPage:   int(totalPage),
		List:        list,
	}
	return serializer.Response{Data: res}
}

// 分享查看
func (service *ShareViewService) View(c *gin.Context) serializer.Response {
	share := model.GetShareByHashID(service.Hash)
	userId := share.UserID
	nodeId := share.NodeID
	user, ok := cache.Get(fmt.Sprintf("user_%s", userId))
	if !ok {
		return serializer.Err(serializer.CodeNotFound, "分享已失效", nil)
	}
	u := user.(teambition.UserInfo)
	client, _ := teambition.NewClient(teambition.WithClientCookie(u.Cookies), teambition.WithClientUser(userId))
	resp, err := client.GetItem(nodeId, false)
	if err != nil {
		return serializer.Err(serializer.CodeNotFound, err.Error(), nil)
	}
	return serializer.Response{Data: resp}
}

// 分享创建
func (service *ShareCreateService) Create(c *gin.Context, user *teambition.UserInfo) serializer.Response {
	newShare := model.Share{
		NodeID: service.NodeID,
		Name:   service.Name,
		UserID: user.User.ID,
		IsDir:  false,
	}
	id, err := newShare.Create()
	if err != nil {
		return serializer.Err(serializer.CodeDBError, "分享链接创建失败", err)
	}
	// 获取分享的唯一id
	code := hashid.HashID(id, hashid.ShareID)
	return serializer.Response{Data: code}
}

// 分享删除
func (service *ShareDeleteService) Delete(c *gin.Context, user *teambition.UserInfo) serializer.Response {
	var share model.Share
	result := model.DB.First(&share, service.ID)
	if result.Error != nil {
		return serializer.Err(serializer.CodeNotFound, "资源未找到", nil)
	}
	err := share.Delete()
	if err != nil {
		return serializer.Err(serializer.CodeNotFound, err.Error(), nil)
	}
	return serializer.Response{}
}
