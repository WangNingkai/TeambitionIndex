package user

import (
	"fmt"
	"teambition-index/pkg/cache"
	"teambition-index/pkg/conf"
	"teambition-index/pkg/serializer"
	"teambition-index/pkg/teambition"
	"time"

	jwt "github.com/form3tech-oss/jwt-go"
	"github.com/gin-gonic/gin"
)

// UserLoginService 管理用户登录的服务
type UserLoginService struct {
	UserName string `json:"userName" binding:"required"`
	Password string ` json:"password" binding:"required"`
}

// Authenticate 用户登录
func (service *UserLoginService) Authenticate(c *gin.Context) serializer.Response {
	client, _ := teambition.NewClient()
	res, err := client.Login(service.UserName, service.Password)
	if err != nil {
		return serializer.Err(serializer.CodeCredentialInvalid, err.Error(), nil)
	}
	user := res.User
	_ = cache.Set(fmt.Sprintf("user_%s", user.ID), res, -1)

	// Create token
	token := jwt.New(jwt.SigningMethodHS256)

	// Set claims
	claims := token.Claims.(jwt.MapClaims)
	claims["name"] = user.Name
	claims["uid"] = user.ID
	claims["exp"] = time.Now().Add(time.Hour * 72).Unix()

	// Generate encoded token and send it as response.
	access_token, err := token.SignedString([]byte(conf.SystemConfig.JWTSecret))
	if err != nil {
		return serializer.Err(serializer.CodeParamErr, "", nil)
	}

	return serializer.Response{Data: gin.H{
		"user":  user,
		"token": access_token,
	}}
}
