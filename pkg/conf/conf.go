package conf

import (
	"teambition-index/pkg/util"

	"github.com/go-ini/ini"
	"github.com/go-playground/validator/v10"
)

// database 数据库
type database struct {
	Type        string
	User        string
	Password    string
	Host        string
	Name        string
	TablePrefix string
	DBFile      string
	Port        int
}

// system 系统通用配置
type system struct {
	Host      string `validate:"required"`
	Port      string `validate:"required"`
	Debug     bool
	AppSecret string
	JWTSecret string
	WorkerNum int
}

// redis 配置
type redis struct {
	Network  string
	Server   string
	Password string
	DB       string
}

// 跨域配置
type cors struct {
	AllowOrigins     []string
	AllowMethods     []string
	AllowHeaders     []string
	AllowCredentials bool
	ExposeHeaders    []string
}

var cfg *ini.File

const defaultConf = `[System]
Host = 127.0.0.1
Port = 3000
AppSecret = {AppSecret}
JWTSecret = {JWTSecret}
`

// Init 初始化配置文件
func Init(path string) {
	var err error

	if path == "" || !util.Exists(path) {
		// 创建初始配置文件
		confContent := util.Replace(map[string]string{
			"{AppSecret}": util.RandStringRunes(64),
			"{JWTSecret}": util.RandStringRunes(64),
		}, defaultConf)
		f, err := util.CreatNestedFile(path)
		if err != nil {
			util.Log().Panic("无法创建配置文件, %s", err)
		}

		// 写入配置文件
		_, err = f.WriteString(confContent)
		if err != nil {
			util.Log().Panic("无法写入配置文件, %s", err)
		}

		f.Close()
	}

	cfg, err = ini.Load(path)
	if err != nil {
		util.Log().Panic("无法解析配置文件 '%s': %s", path, err)
	}

	sections := map[string]interface{}{
		"System":   SystemConfig,
		"Database": DatabaseConfig,
		"Redis":    RedisConfig,
		"CORS":     CORSConfig,
	}
	for sectionName, sectionStruct := range sections {
		err = mapSection(sectionName, sectionStruct)
		if err != nil {
			util.Log().Panic("配置文件 %s 分区解析失败: %s", sectionName, err)
		}
	}

	// 重设log等级
	if !SystemConfig.Debug {
		util.Level = util.LevelInformational
		util.GloablLogger = nil
		util.Log()
	}
}

// mapSection 将配置文件的 Section 映射到结构体上
func mapSection(section string, confStruct interface{}) error {
	err := cfg.Section(section).MapTo(confStruct)
	if err != nil {
		return err
	}

	// 验证合法性
	validate := validator.New()
	err = validate.Struct(confStruct)
	if err != nil {
		return err
	}

	return nil
}
