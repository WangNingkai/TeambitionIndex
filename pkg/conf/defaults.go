package conf

// RedisConfig Redis服务器配置
var RedisConfig = &redis{
	Network:  "tcp",
	Server:   "",
	Password: "",
	DB:       "0",
}

// DatabaseConfig 数据库配置
var DatabaseConfig = &database{
	Type:   "UNSET",
	DBFile: "teambition-index.db",
	Port:   3306,
}

// SystemConfig 系统公用配置
var SystemConfig = &system{
	Debug:         false,
	ServerAddress: ":3000",
}

// CORSConfig 跨域配置
var CORSConfig = &cors{
	AllowOrigins:     []string{"*"},
	AllowMethods:     []string{"PUT", "POST", "GET", "PATCH", "OPTIONS"},
	AllowHeaders:     []string{"Cookie", "X-Policy", "Authorization", "Content-Length", "Content-Type", "X-Path", "X-FileName"},
	AllowCredentials: false,
	ExposeHeaders:    nil,
}
