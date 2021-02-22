package middleware

import (
	"errors"
	"fmt"
	"reflect"
	"strings"
	"teambition-index/pkg/cache"
	"teambition-index/pkg/serializer"

	"github.com/form3tech-oss/jwt-go"
	"github.com/gin-gonic/gin"
)

type JwtConf struct {
	// Filter defines a function to skip middleware.
	// Optional. Default: nil
	Filter func(*gin.Context) bool

	// SuccessHandler defines a function which is executed for a valid token.
	// Optional. Default: nil
	SuccessHandler func(*gin.Context)

	// ErrorHandler defines a function which is executed for an invalid token.
	// It may be used to define a custom JWT error.
	// Optional. Default: 401 Invalid or expired JWT
	ErrorHandler func(*gin.Context, error)

	// Signing key to validate token. Used as fallback if SigningKeys has length 0.
	// Required. This or SigningKeys.
	SigningKey interface{}

	// Map of signing keys to validate token with kid field usage.
	// Required. This or SigningKey.
	SigningKeys map[string]interface{}

	// Signing method, used to check token signing method.
	// Optional. Default: "HS256".
	// Possible values: "HS256", "HS384", "HS512", "ES256", "ES384", "ES512", "RS256", "RS384", "RS512"
	SigningMethod string

	// Context key to store user information from the token into context.
	// Optional. Default: "user".
	ContextKey string

	// Claims are extendable claims data defining token content.
	// Optional. Default value jwt.MapClaims
	Claims jwt.Claims

	// TokenLookup is a string in the form of "<source>:<name>" that is used
	// to extract token from the request.
	// Optional. Default value "header:Authorization".
	// Possible values:
	// - "header:<name>"
	// - "query:<name>"
	// - "param:<name>"
	// - "cookie:<name>"
	TokenLookup string

	// AuthScheme to be used in the Authorization header.
	// Optional. Default: "Bearer".
	AuthScheme string

	keyFunc jwt.Keyfunc
}

// JwtAuth Jwt中间件
func JwtAuth(config ...JwtConf) gin.HandlerFunc {
	// Init config
	var cfg JwtConf
	if len(config) > 0 {
		cfg = config[0]
	}
	if cfg.SuccessHandler == nil {
		cfg.SuccessHandler = func(c *gin.Context) {
			c.Next()
		}
	}
	if cfg.ErrorHandler == nil {
		cfg.ErrorHandler = func(c *gin.Context, err error) {
			if err.Error() == "Missing or malformed JWT" {
				c.JSON(200, serializer.Err(serializer.CodeCredentialInvalid, "Missing or malformed Token", nil))
				c.Abort()
			} else {
				c.JSON(200, serializer.Err(serializer.CodeCredentialInvalid, "Invalid or expired Token", nil))
				c.Abort()
			}
		}
	}
	if cfg.SigningKey == nil && len(cfg.SigningKeys) == 0 {
		panic("Fiber: JWT middleware requires signing key")
	}
	if cfg.SigningMethod == "" {
		cfg.SigningMethod = "HS256"
	}
	if cfg.ContextKey == "" {
		cfg.ContextKey = "user"
	}
	if cfg.Claims == nil {
		cfg.Claims = jwt.MapClaims{}
	}
	if cfg.TokenLookup == "" {
		cfg.TokenLookup = "header:Authorization"
	}
	if cfg.AuthScheme == "" {
		cfg.AuthScheme = "Bearer"
	}
	cfg.keyFunc = func(t *jwt.Token) (interface{}, error) {
		// Check the signing method
		if t.Method.Alg() != cfg.SigningMethod {
			return nil, fmt.Errorf("Unexpected jwt signing method=%v", t.Header["alg"])
		}
		if len(cfg.SigningKeys) > 0 {
			if kid, ok := t.Header["kid"].(string); ok {
				if key, ok := cfg.SigningKeys[kid]; ok {
					return key, nil
				}
			}
			return nil, fmt.Errorf("Unexpected jwt key id=%v", t.Header["kid"])
		}
		return cfg.SigningKey, nil
	}
	// Initialize
	extractors := make([]func(c *gin.Context) (string, error), 0)
	rootParts := strings.Split(cfg.TokenLookup, ",")
	for _, rootPart := range rootParts {
		parts := strings.Split(strings.TrimSpace(rootPart), ":")

		switch parts[0] {
		case "header":
			extractors = append(extractors, jwtFromHeader(parts[1], cfg.AuthScheme))
		case "query":
			extractors = append(extractors, jwtFromQuery(parts[1]))
		case "param":
			extractors = append(extractors, jwtFromParam(parts[1]))
		case "cookie":
			extractors = append(extractors, jwtFromCookie(parts[1]))
		}
	}
	// Return middleware handler
	return func(c *gin.Context) {
		// Filter request to skip middleware
		if cfg.Filter != nil && cfg.Filter(c) {
			c.Next()
			return
		}
		var auth string
		var err error

		for _, extractor := range extractors {
			auth, err = extractor(c)
			if auth != "" && err == nil {
				break
			}
		}

		if err != nil {
			cfg.ErrorHandler(c, err)
			return
		}
		token := new(jwt.Token)

		if _, ok := cfg.Claims.(jwt.MapClaims); ok {
			token, err = jwt.Parse(auth, cfg.keyFunc)
		} else {
			t := reflect.ValueOf(cfg.Claims).Type().Elem()
			claims := reflect.New(t).Interface().(jwt.Claims)
			token, err = jwt.ParseWithClaims(auth, claims, cfg.keyFunc)
		}
		if err == nil && token.Valid {
			// Store user information from token into context.
			claims := token.Claims.(jwt.MapClaims)
			uid := claims["uid"]
			if user, ok := cache.Get(fmt.Sprintf("user_%s", uid)); ok {
				c.Set(cfg.ContextKey, user)
			}
			cfg.SuccessHandler(c)
			return
		}
		cfg.ErrorHandler(c, err)
	}
}

// jwtFromHeader returns a function that extracts token from the request header.
func jwtFromHeader(header string, authScheme string) func(c *gin.Context) (string, error) {
	return func(c *gin.Context) (string, error) {
		auth := c.Request.Header.Get(header)
		l := len(authScheme)
		if len(auth) > l+1 && auth[:l] == authScheme {
			return auth[l+1:], nil
		}
		return "", errors.New("Missing or malformed JWT")
	}
}

// jwtFromQuery returns a function that extracts token from the query string.
func jwtFromQuery(param string) func(c *gin.Context) (string, error) {
	return func(c *gin.Context) (string, error) {
		token := c.Query(param)
		if token == "" {
			return "", errors.New("Missing or malformed JWT")
		}
		return token, nil
	}
}

// jwtFromParam returns a function that extracts token from the url param string.
func jwtFromParam(param string) func(c *gin.Context) (string, error) {
	return func(c *gin.Context) (string, error) {
		token := c.Param(param)
		if token == "" {
			return "", errors.New("Missing or malformed JWT")
		}
		return token, nil
	}
}

// jwtFromCookie returns a function that extracts token from the named cookie.
func jwtFromCookie(name string) func(c *gin.Context) (string, error) {
	return func(c *gin.Context) (string, error) {
		token, err := c.Cookie(name)
		if err != nil {
			return "", errors.New("Missing or malformed JWT")
		}
		if token == "" {
			return "", errors.New("Missing or malformed JWT")
		}
		return token, nil
	}
}
