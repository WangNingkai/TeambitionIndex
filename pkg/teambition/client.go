package teambition

import (
	"errors"
	"fmt"
	"net/http"
	"teambition-index/pkg/cache"
)

// Client OneDrive客户端
type Client struct {
	UA       string
	Cookie   []*http.Cookie
	UserID   string
	OrgID    string
	SpaceID  string
	DriveID  string
	MemberID string
	RootID   string
}

func NewClient(opts ...ClientOption) (*Client, error) {
	clientOptions := newDefaultClientOption()
	for _, o := range opts {
		o.apply(clientOptions)
	}
	client := &Client{
		Cookie: clientOptions.Cookie,
		UA:     clientOptions.UA,
		UserID: clientOptions.UserID,
	}
	useCache := false
	if clientOptions.Cookie != nil && clientOptions.UserID != "" {
		if pan, ok := cache.Get(fmt.Sprintf("user_pan_%s", clientOptions.UserID)); ok {
			if data, ok := pan.(Pan); ok {
				useCache = true
				client.OrgID = data.OrgID
				client.MemberID = data.MemberID
				client.SpaceID = data.SpaceID
				client.DriveID = data.DriveID
				client.RootID = data.RootID
			}
		}
	}
	if !useCache {
		resp, err := client.GetPan()
		if err != nil {
			return nil, errors.New("无法解析授权端点地址")
		}
		client.OrgID = resp.OrgID
		client.MemberID = resp.MemberID
		client.SpaceID = resp.SpaceID
		client.DriveID = resp.DriveID
		client.RootID = resp.RootID
	}

	return client, nil
}
