package context

import (
	"flag"
	"fmt"
	"teambition-index/pkg/conf"
	"teambition-index/pkg/util"
	"time"

	"gorm.io/driver/mysql"
	"gorm.io/driver/sqlite"
	"gorm.io/gorm"
	"gorm.io/gorm/logger"
	"gorm.io/gorm/schema"
)

var version string = "UNKOWN VERSION"

//Context 上下文对象
type Context struct {
	ConfPath      string
	Version       string
	AppName       string
	ServerAddress string
	ServerHost    string
	ServerPort    string
	Debug         bool
	AppSecret     string
	DB            *gorm.DB
}

var instance = new(Context)
var isFlagBind = false

func init() {
	bindFlags()
}

func bindFlags() {
	if !isFlagBind {
		flag.StringVar(&instance.ConfPath, "c", util.RelativePath("conf.ini"), "配置文件路径")
		isFlagBind = true
	}
}

//InitContext 初始化Context,只能初始化一次
func InitContext() {
	flag.Parse()
	conf.Init(instance.ConfPath)
	instance.Version = version
	instance.AppName = "teambition-index"
	instance.ServerHost = conf.SystemConfig.Host
	instance.ServerPort = conf.SystemConfig.Port
	instance.Debug = conf.SystemConfig.Debug
	instance.ServerAddress = fmt.Sprintf("%s:%s", instance.ServerHost, instance.ServerPort)
	instance.AppSecret = conf.SystemConfig.AppSecret
	instance.DB = instance.initDatabase()
}

//Get 返回当前Context实例
func Get() *Context {
	return instance
}

// initDatabase 初始化数据库
func (context *Context) initDatabase() *gorm.DB {
	util.Log().Info("初始化数据库连接")
	var (
		db  *gorm.DB
		err error
	)

	dbConfig := gorm.Config{
		NamingStrategy: schema.NamingStrategy{
			TablePrefix:   conf.DatabaseConfig.TablePrefix, // 表名前缀，
			SingularTable: true,                            // 使用单数表名，启用该选项
		},
	}
	if !conf.SystemConfig.Debug {
		dbConfig.Logger = logger.Default.LogMode(logger.Silent)
	} else {
		dbConfig.Logger = logger.Default.LogMode(logger.Info)
	}
	if conf.SystemConfig.Debug {
		// db, err = gorm.Open(sqlite.Open("file::memory:?cache=shared"), &dbConfig)
		db, err = gorm.Open(sqlite.Open("teambition-index.db"), &dbConfig)
	} else {
		switch conf.DatabaseConfig.Type {
		case "UNSET", "sqlite", "sqlite3":
			// 未指定数据库或者明确指定为 sqlite 时，使用 SQLite3 数据库
			db, err = gorm.Open(sqlite.Open(util.RelativePath(conf.DatabaseConfig.DBFile)), &dbConfig)
		case "mysql":
			// 当前只支持 sqlite3 与 mysql 数据库
			db, err = gorm.Open(mysql.Open(fmt.Sprintf("%s:%s@(%s:%d)/%s?charset=utf8mb4&parseTime=True&loc=Local",
				conf.DatabaseConfig.User,
				conf.DatabaseConfig.Password,
				conf.DatabaseConfig.Host,
				conf.DatabaseConfig.Port,
				conf.DatabaseConfig.Name)), &dbConfig,
			)
		default:
			util.Log().Panic("不支持数据库类型: %s", conf.DatabaseConfig.Type)
		}
	}

	if err != nil {
		util.Log().Panic("连接数据库不成功, %s", err)
	}

	//设置连接池
	//空闲
	sqlDB, err := db.DB()
	if err != nil {
		util.Log().Panic("设置数据库连接池不成功, %s", err)
	}
	sqlDB.SetMaxIdleConns(50)
	//打开
	sqlDB.SetMaxOpenConns(100)
	//超时
	sqlDB.SetConnMaxLifetime(time.Second * 30)

	return db
}
