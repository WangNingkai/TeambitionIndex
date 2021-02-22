package teambition

import (
	"net/http"
	"time"
)

// Option 发送请求的额外设置
type ClientOption interface {
	apply(*Client)
}

type clientOptionFunc func(*Client)

func (f clientOptionFunc) apply(o *Client) {
	f(o)
}

func WithClientUA(ua string) ClientOption {
	return clientOptionFunc(func(o *Client) {
		o.UA = ua
	})
}

func WithClientCookie(cookie []*http.Cookie) ClientOption {
	return clientOptionFunc(func(o *Client) {
		o.Cookie = cookie
	})
}

func WithClientUser(ID string) ClientOption {
	return clientOptionFunc(func(o *Client) {
		o.UserID = ID
	})
}

func newDefaultClientOption() *Client {
	return &Client{
		UA: "Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/87.0.4280.66 Safari/537.36",
	}
}

type graphOptions struct {
	timeout time.Duration
	headers map[string]string
	cookies []*http.Cookie
}

// Option 发送请求的额外设置
type GraphOption interface {
	apply(*graphOptions)
}

type graphOptionFunc func(*graphOptions)

func (f graphOptionFunc) apply(o *graphOptions) {
	f(o)
}

// WithTimeout 设置请求超时
func WithGraphTimeout(t time.Duration) GraphOption {
	return graphOptionFunc(func(o *graphOptions) {
		o.timeout = t
	})
}

// WithHeader 设置请求Header
func WithGraphHeaders(headers map[string]string) GraphOption {
	return graphOptionFunc(func(o *graphOptions) {
		for k, v := range headers {
			o.headers[k] = v
		}
	})
}

// WithHeader 设置请求Header
func WithGraphCookies(cookies []*http.Cookie) GraphOption {
	return graphOptionFunc(func(o *graphOptions) {
		o.cookies = cookies
	})
}

// newDefaultGraphOption 默认GraphOption
func newDefaultGraphOption() *graphOptions {
	return &graphOptions{
		headers: map[string]string{
			"Content-Type": "application/json",
			"Accept":       "application/json",
			"User-Agent":   "Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/87.0.4280.66 Safari/537.36",
		},
		timeout: time.Duration(30) * time.Second,
	}
}
