package controllers

import (
	"encoding/json"
	"strings"
	"teambition-index/pkg/serializer"
	"teambition-index/pkg/teambition"

	"github.com/gin-gonic/gin"
	"github.com/go-playground/validator/v10"
)

// ParamErrorMsg 根据Validator返回的错误信息给出错误提示
func ParamErrorMsg(filed string, tag string) string {
	// 未通过验证的表单域与中文对应
	fieldMap := map[string]string{
		"UserName": "邮箱",
		"Password": "密码",
		"Path":     "路径",
		"SourceID": "原始资源",
		"URL":      "链接",
		"Nick":     "昵称",
	}
	// 未通过的规则与中文对应
	tagMap := map[string]string{
		"required": "不能为空",
		"min":      "太短",
		"max":      "太长",
		"email":    "格式不正确",
	}
	fieldVal, findField := fieldMap[filed]
	tagVal, findTag := tagMap[tag]
	if findField && findTag {
		var builder strings.Builder
		builder.WriteString(fieldVal)
		builder.WriteString(tagVal)
		// 返回拼接出来的错误信息
		return builder.String()
	}
	return ""
}

// ErrorResponse 返回错误消息
func ErrorResponse(err error) serializer.Response {
	// 处理 Validator 产生的错误
	if ve, ok := err.(validator.ValidationErrors); ok {
		for _, e := range ve {
			return serializer.ParamErr(
				ParamErrorMsg(e.Field(), e.Tag()),
				err,
			)
		}
	}

	if _, ok := err.(*json.UnmarshalTypeError); ok {
		return serializer.ParamErr("JSON类型不匹配", err)
	}

	return serializer.ParamErr("参数错误", err)
}

// CurrentUser 获取当前用户
func CurrentUser(c *gin.Context) *teambition.UserInfo {
	if user, _ := c.Get("user"); user != nil {
		if u, ok := user.(teambition.UserInfo); ok {
			return &u
		}
	}
	return nil
}
