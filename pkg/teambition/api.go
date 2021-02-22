package teambition

import (
	"encoding/json"
	"fmt"
	"net/url"
	"teambition-index/pkg/cache"
	"teambition-index/pkg/util"

	"github.com/go-resty/resty/v2"
	"github.com/unknwon/com"
)

// GetOrgID 获取OrgID
func (client *Client) GetOrgID() (*OrgResponse, error) {
	opts := WithGraphCookies(client.Cookie)
	resp, err := client.request("GET", "https://www.teambition.com/api/organizations/personal", "", opts)
	if err != nil {
		return nil, err
	}
	var (
		decodeErr error
		data      OrgResponse
	)
	decodeErr = json.Unmarshal(resp.Body(), &data)
	if decodeErr != nil {
		return nil, decodeErr
	}

	return &data, nil
}

// GetDriveID 获取DriveID
func (client *Client) GetDriveID(OrgID string) (*DriveResponse, error) {
	opts := WithGraphCookies(client.Cookie)
	URL := fmt.Sprintf("https://pan.teambition.com/pan/api/orgs/%s", OrgID)
	resp, err := client.request("GET", URL, "", opts)
	if err != nil {
		return nil, err
	}
	var (
		decodeErr error
		data      DriveResponse
	)
	decodeErr = json.Unmarshal(resp.Body(), &data)
	if decodeErr != nil {
		return nil, decodeErr
	}

	return &data, nil
}

// GetSpaceID 获取SpaceID
func (client *Client) GetSpaceID(OrgID string, MemberID string) ([]SpaceResponse, error) {
	opts := WithGraphCookies(client.Cookie)
	URL := fmt.Sprintf("https://pan.teambition.com/pan/api/spaces/?orgId=%s&memberId=%s", OrgID, MemberID)
	resp, err := client.request("GET", URL, "", opts)
	if err != nil {
		return nil, err
	}
	var (
		decodeErr error
		data      []SpaceResponse
	)
	decodeErr = json.Unmarshal(resp.Body(), &data)
	if decodeErr != nil {
		return nil, decodeErr
	}

	return data, nil
}

// GetPan 获取PAN
func (client *Client) GetPan() (*Pan, error) {
	orgResp, err := client.GetOrgID()
	if err != nil {
		return nil, err
	}
	orgId := orgResp.OrgID
	memberId := orgResp.MemberID
	spaceResp, err := client.GetSpaceID(orgId, memberId)
	if err != nil {
		return nil, err
	}
	space := spaceResp[0]
	rootId := space.RootID
	spaceId := space.SpaceID
	driveResp, err := client.GetDriveID(orgId)
	if err != nil {
		return nil, err
	}
	driveId := driveResp.Data.DriveID
	pan := Pan{
		OrgID:    orgId,
		DriveID:  driveId,
		SpaceID:  spaceId,
		RootID:   rootId,
		MemberID: memberId,
	}
	_ = cache.Set(fmt.Sprintf("user_pan_%s", client.UserID), pan, -1)
	return &pan, nil

}

// GetList 获取列表
func (client *Client) GetList(NodeID string, Limit int64, Offset int64, useCache bool) (*ListResponse, error) {
	if NodeID == "" {
		NodeID = client.RootID
	}
	if useCache {
		if cachedData, ok := cache.Get(fmt.Sprintf("s_list_%s_%s_%d_%d", client.UserID, NodeID, Limit, Offset)); ok {
			data := cachedData.(*ListResponse)
			return data, nil
		}

	}
	opts := WithGraphCookies(client.Cookie)
	URL, _ := url.Parse("https://pan.teambition.com/pan/api/nodes")

	query := url.Values{
		"orgId":          {client.OrgID},
		"spaceId":        {client.SpaceID},
		"driveId":        {client.DriveID},
		"parentId":       {NodeID},
		"offset":         {com.ToStr(Offset)},
		"limit":          {com.ToStr(Limit)},
		"orderBy":        {"updateTime"},
		"orderDirection": {"desc"},
	}
	URL.RawQuery = query.Encode()

	resp, err := client.request("GET", URL.String(), "", opts)
	if err != nil {
		return nil, err
	}
	var (
		decodeErr error
		data      ListResponse
	)
	decodeErr = json.Unmarshal(resp.Body(), &data)
	if decodeErr != nil {
		return nil, decodeErr
	}
	_ = cache.Set(
		fmt.Sprintf("s_list_%s_%s_%d_%d", client.UserID, NodeID, Limit, Offset),
		&data,
		1800,
	)
	return &data, nil
}

// GetItem 获取详情
func (client *Client) GetItem(NodeID string, useCache bool) (*ItemResponse, error) {
	if NodeID == "" {
		NodeID = client.RootID
	}
	if useCache {
		if cachedData, ok := cache.Get(fmt.Sprintf("s_item_%s_%s", client.UserID, NodeID)); ok {
			data := cachedData.(*ItemResponse)
			return data, nil
		}
	}
	if NodeID == "" {
		NodeID = client.RootID
	}
	opts := WithGraphCookies(client.Cookie)
	rawURL := fmt.Sprintf("https://pan.teambition.com/pan/api/nodes/%s", NodeID)
	URL, _ := url.Parse(rawURL)

	query := url.Values{
		"orgId":   {client.OrgID},
		"spaceId": {client.SpaceID},
		"driveId": {client.DriveID},
	}
	URL.RawQuery = query.Encode()

	resp, err := client.request("GET", URL.String(), "", opts)
	if err != nil {
		return nil, err
	}
	var (
		decodeErr error
		data      ItemResponse
	)
	decodeErr = json.Unmarshal(resp.Body(), &data)
	if decodeErr != nil {
		return nil, decodeErr
	}
	_ = cache.Set(
		fmt.Sprintf("s_item_%s_%s", client.UserID, NodeID),
		&data,
		1800,
	)
	return &data, nil
}

// request 请求
func (client *Client) request(method string, url string, body interface{}, options ...GraphOption) (*resty.Response, error) {
	options = append(options,
		WithGraphHeaders(map[string]string{
			"Content-Type": "application/json",
		}),
	)
	graphOptions := newDefaultGraphOption()
	for _, o := range options {
		o.apply(graphOptions)
	}

	// 发送请求
	_client := resty.New()
	_client.SetTimeout(graphOptions.timeout)
	_client.SetRetryCount(3)
	req := _client.R()
	req.Method = method
	req.URL = url
	req.Cookies = graphOptions.cookies

	resp, err := req.
		SetHeaders(graphOptions.headers).
		SetBody(body).
		Send()

	// fmt.Println("Request Info:")
	// fmt.Println("  Url :", req.URL)
	// fmt.Println("  Body      :", req.Body)
	// fmt.Println("  Headers :", req.Header)
	// fmt.Println()

	// fmt.Println("Response Info:")
	// fmt.Println("  Error      :", err)
	// fmt.Println("  Status Code:", resp.StatusCode())
	// fmt.Println("  Status     :", resp.Status())
	// fmt.Println("  Proto      :", resp.Proto())
	// fmt.Println("  Time       :", resp.Time())
	// fmt.Println("  Received At:", resp.ReceivedAt())
	// fmt.Println("  Body       :\n", resp)
	// fmt.Println("  Headers :", resp.Header())
	// fmt.Println("  Cookie :", resp.Cookies())
	// fmt.Println()

	if err != nil {
		return nil, err
	}
	respBody := resp.Body()

	// 解析请求响应
	var (
		errResp   interface{}
		decodeErr error
	)
	// 如果有错误
	if resp.StatusCode() < 200 || resp.StatusCode() >= 300 {
		decodeErr = json.Unmarshal([]byte(respBody), &errResp)
		if decodeErr != nil {
			util.Log().Debug("Teambition返回未知响应[%s]", respBody)
			return nil, decodeErr
		}
		return resp, nil
	}

	return resp, nil
}
