package controllers

import (
	"teambition-index/app/service/share"

	"github.com/gin-gonic/gin"
)

// ShareList 分享列表
func ShareList(c *gin.Context) {
	var service share.ShareListService
	if err := c.ShouldBindQuery(&service); err == nil {
		res := service.List(c, CurrentUser(c))
		c.JSON(200, res)
	} else {
		c.JSON(200, ErrorResponse(err))
	}
}

// ShareCreate 分享创建
func ShareCreate(c *gin.Context) {
	var service share.ShareCreateService
	if err := c.ShouldBindJSON(&service); err == nil {
		res := service.Create(c, CurrentUser(c))
		c.JSON(200, res)
	} else {
		c.JSON(200, ErrorResponse(err))
	}
}

// ShareList 分享删除
func ShareDelete(c *gin.Context) {
	var service share.ShareDeleteService
	if err := c.ShouldBindUri(&service); err == nil {
		res := service.Delete(c, CurrentUser(c))
		c.JSON(200, res)
	} else {
		c.JSON(200, ErrorResponse(err))
	}
}

// ShareView 分享查看
func ShareView(c *gin.Context) {
	var service share.ShareViewService
	if err := c.ShouldBindUri(&service); err == nil {
		res := service.View(c)
		c.JSON(200, res)
	} else {
		c.JSON(200, ErrorResponse(err))
	}
}
