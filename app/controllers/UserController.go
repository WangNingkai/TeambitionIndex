package controllers

import (
	"teambition-index/app/service/user"
	"teambition-index/pkg/serializer"
	"teambition-index/pkg/teambition"

	"github.com/gin-gonic/gin"
)

// UserLogin 用户登录
func UserLogin(c *gin.Context) {
	var service user.UserLoginService
	if err := c.ShouldBindJSON(&service); err == nil {
		res := service.Authenticate(c)
		c.JSON(200, res)
	} else {
		c.JSON(200, ErrorResponse(err))
	}
}

// UserInit 用户pan初始化
func UserInit(c *gin.Context) {
	user := CurrentUser(c)
	if user == nil {
		c.JSON(200, serializer.Err(serializer.CodeCredentialInvalid, "用户不存在", nil))
		return
	}
	cookies := user.Cookies
	client, _ := teambition.NewClient(teambition.WithClientCookie(cookies), teambition.WithClientUser(user.User.ID))
	res, err := client.GetPan()
	if err != nil {
		c.JSON(200, serializer.Err(serializer.CodeCredentialInvalid, err.Error(), nil))
	} else {
		c.JSON(200, serializer.Response{Data: res})
	}
}
