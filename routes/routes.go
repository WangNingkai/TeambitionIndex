package routes

import (
	"teambition-index/app/controllers"
	"teambition-index/app/middleware"
	"teambition-index/pkg/conf"

	"github.com/gin-contrib/cors"
	"github.com/gin-contrib/gzip"
	"github.com/gin-gonic/gin"
)

// InitRouter 初始化路由
func InitRouter() *gin.Engine {
	r := gin.Default()

	r.Use(gzip.Gzip(gzip.DefaultCompression, gzip.WithExcludedPaths([]string{"/api/"})))
	r.Use(middleware.FrontendFileHandler())
	// 跨域相关
	InitCORS(r)

	api := r.Group("api")
	share := api.Group("")
	{
		share.GET("share/:hash", controllers.ShareView)
	}
	{
		// 登录
		api.POST("login", controllers.UserLogin)
		// 需要登录保护的
		auth := api.Group("")
		auth.Use(middleware.JwtAuth(middleware.JwtConf{
			SigningKey: []byte(conf.SystemConfig.JWTSecret),
		}))
		{
			// 初始化信息
			auth.GET("init", controllers.UserInit)
			auth.POST("nodes", controllers.NodeList)
			auth.POST("node", controllers.NodeItem)
			auth.GET("share", controllers.ShareList)
			auth.POST("share", controllers.ShareCreate)
			auth.DELETE("share/:id", controllers.ShareDelete)
		}
	}
	return r
}

// InitCORS 初始化跨域配置
func InitCORS(router *gin.Engine) {
	if conf.CORSConfig.AllowOrigins[0] != "UNSET" {
		router.Use(cors.New(cors.Config{
			AllowOrigins:     conf.CORSConfig.AllowOrigins,
			AllowMethods:     conf.CORSConfig.AllowMethods,
			AllowHeaders:     conf.CORSConfig.AllowHeaders,
			AllowCredentials: conf.CORSConfig.AllowCredentials,
			ExposeHeaders:    conf.CORSConfig.ExposeHeaders,
		}))
		return
	}
}
