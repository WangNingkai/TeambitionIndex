package controllers

import (
	"teambition-index/app/service/node"

	"github.com/gin-gonic/gin"
)

// NodeList 列表
func NodeList(c *gin.Context) {
	var service node.NodesListService
	if err := c.ShouldBindJSON(&service); err == nil {
		res := service.Fetch(c, CurrentUser(c))
		c.JSON(200, res)
	} else {
		c.JSON(200, ErrorResponse(err))
	}
}

// 详情
func NodeItem(c *gin.Context) {
	var service node.NodeService
	if err := c.ShouldBindJSON(&service); err == nil {
		res := service.Fetch(c, CurrentUser(c))
		c.JSON(200, res)
	} else {
		c.JSON(200, ErrorResponse(err))
	}
}
