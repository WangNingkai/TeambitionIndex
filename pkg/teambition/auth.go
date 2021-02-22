package teambition

import (
	"encoding/json"
	"fmt"
	"regexp"
	"strings"
)

// GetLoginToken 获取登录Token
func (client *Client) GetLoginToken() (string, error) {
	opts := WithGraphHeaders(map[string]string{
		"User-Agent":   "Mozilla/5.0 (iPhone; CPU iPhone OS 13_2_3 like Mac OS X) AppleWebKit/605.1.15 (KHTML, like Gecko) Version/13.0.3 Mobile/15E148 Safari/604.1 Edg/88.0.4324.182",
		"Accept":       "text/html,application/xhtml+xml,application/xml;q=0.9,image/webp,image/apng,*/*;q=0.8,application/signed-exchange;v=b3;q=0.9",
		"Content-Type": "text/html; charset=utf-8",
	})
	resp, err := client.request("GET", "https://account.teambition.com/login/password", "", opts)
	if err != nil {
		return "", err
	}
	reg := regexp.MustCompile(`"TOKEN":"([a-zA-Z0-9_\-\.]+)"`)
	token := reg.FindAllString(resp.String(), -1)[0]
	token = strings.Split(token, ":")[1]
	token = strings.Trim(token, "\"")

	return token, nil
}

// Login 登录
func (client *Client) Login(username string, password string) (*UserInfo, error) {
	token, err := client.GetLoginToken()
	if err != nil {
		return nil, err
	}
	body := map[string]string{
		"password":      password,
		"token":         token,
		"client_id":     "90727510-5e9f-11e6-bf41-15ed35b6cc41",
		"response_type": "session",
	}

	endpoint := "email"
	reg := regexp.MustCompile(`\w+([-+.]\w+)*@\w+([-.]\w+)*\.\w+([-.]\w+)*`)
	if !reg.MatchString(username) {
		endpoint = "phone"
		body["phone"] = username
	} else {
		body["email"] = username
	}
	bodyBytes, _ := json.Marshal(body)
	URL := fmt.Sprintf("https://account.teambition.com/api/login/%s", endpoint)
	resp, err := client.request("POST", URL, string(bodyBytes))
	if err != nil {
		return nil, err
	}

	var (
		decodeErr error
		data      UserInfo
	)
	decodeErr = json.Unmarshal(resp.Body(), &data)
	if decodeErr != nil {
		return nil, decodeErr
	}

	data.Cookies = resp.Cookies()

	return &data, nil
}
