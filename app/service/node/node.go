package node

import (
	"teambition-index/pkg/serializer"
	"teambition-index/pkg/teambition"

	"github.com/gin-gonic/gin"
)

// NodesListService 资源列表
type NodesListService struct {
	NodeID  string `json:"nodeId"`
	Limit   int64  `json:"limit"`
	Offset  int64  `json:"offset"`
	Refresh bool   `json:"refresh"`
}

// NodeService 资源详情
type NodeService struct {
	NodeID  string `json:"nodeId"`
	Refresh bool   `json:"refresh"`
}

// Fetch 获取列表
func (service *NodesListService) Fetch(c *gin.Context, user *teambition.UserInfo) serializer.Response {
	cookies := user.Cookies
	client, _ := teambition.NewClient(teambition.WithClientCookie(cookies), teambition.WithClientUser(user.User.ID))
	list, err := client.GetList(service.NodeID, service.Limit, service.Offset, !service.Refresh)
	if err != nil {
		return serializer.Err(serializer.CodeNotFound, err.Error(), nil)
	}
	item, err := client.GetItem(service.NodeID, !service.Refresh)
	if err != nil {
		return serializer.Err(serializer.CodeNotFound, err.Error(), nil)
	}
	if service.NodeID == "" {
		service.NodeID = client.RootID
	}
	return serializer.Response{Data: gin.H{
		"limit":      service.Limit,
		"offset":     service.Offset,
		"NextMarker": list.NextMarker,
		"list":       list.Data,
		"item":       item,
		"isRoot":     service.NodeID == client.RootID,
	}}
}

// Fetch 获取详情
func (service *NodeService) Fetch(c *gin.Context, user *teambition.UserInfo) serializer.Response {
	cookies := user.Cookies
	client, _ := teambition.NewClient(teambition.WithClientCookie(cookies), teambition.WithClientUser(user.User.ID))
	resp, err := client.GetItem(service.NodeID, !service.Refresh)
	if err != nil {
		return serializer.Err(serializer.CodeNotFound, err.Error(), nil)
	}
	return serializer.Response{Data: resp}
}
