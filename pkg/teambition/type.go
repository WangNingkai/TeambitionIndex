package teambition

import (
	"encoding/gob"
	"net/http"
	"time"
)

type UserInfo struct {
	User struct {
		ID            string `json:"_id"`
		AvatarUrl     string `json:"avatarUrl"`
		ConturyCode   string `json:"conturyCode"`
		CountryCode   string `json:"countryCode"`
		Email         string `json:"email"`
		Name          string `json:"name"`
		PhoneForLogin string `json:"phoneForLogin"`
		Region        string `json:"region"`
	} `json:"user"`
	Cookies []*http.Cookie `json:"cookies"`
}

type OrgResponse struct {
	OrgID    string `json:"_id"`
	MemberID string `json:"_creatorId"`
}

type SpaceResponse struct {
	SpaceID string `json:"spaceId"`
	RootID  string `json:"rootId"`
}

type DriveResponse struct {
	Data struct {
		DriveID string `json:"driveId"`
	} `json:"data"`
}

type ListResponse struct {
	Data       []ItemResponse `json:"data"`
	NextMarker string         `json:"nextMarker"`
}

type ItemResponse struct {
	ID                 int                `json:"id"`
	Kind               string             `json:"kind"`
	NodeID             string             `json:"nodeId"`
	Name               string             `json:"name"`
	VersionID          string             `json:"versionId"`
	Created            time.Time          `json:"created"`
	Updated            time.Time          `json:"updated"`
	Deleted            interface{}        `json:"deleted"`
	Pos                int                `json:"pos"`
	ParentID           string             `json:"parentId"`
	CcpParentFileID    string             `json:"ccpParentFileId"`
	CcpFileID          string             `json:"ccpFileId"`
	DriveID            string             `json:"driveId"`
	Status             string             `json:"status"`
	Archived           bool               `json:"archived"`
	CreatorID          string             `json:"creatorId"`
	ArchivedBy         string             `json:"archivedBy"`
	Creator            Creator            `json:"creator"`
	Hidden             bool               `json:"hidden"`
	Starred            bool               `json:"starred"`
	ContainerID        string             `json:"containerId"`
	PunishFlag         int                `json:"punishFlag"`
	IsBanned           bool               `json:"isBanned"`
	Ext                string             `json:"ext"`
	Category           string             `json:"category"`
	Thumbnail          string             `json:"thumbnail"`
	DownloadURL        string             `json:"downloadUrl"`
	URL                string             `json:"url"`
	Size               int                `json:"size"`
	VideoMediaMetadata VideoMediaMetadata `json:"videoMediaMetadata"`
	IsSpriteReady      bool               `json:"isSpriteReady"`
	PreviewData        PreviewData        `json:"previewData"`
	DiscussionCount    int                `json:"discussionCount"`
}

type VideoMediaMetadata struct {
}

type Creator struct {
	ID            string `json:"_id"`
	Name          string `json:"name"`
	Email         string `json:"email"`
	AvatarURL     string `json:"avatarUrl"`
	Region        string `json:"region"`
	Lang          string `json:"lang"`
	IsRobot       bool   `json:"isRobot"`
	OpenID        string `json:"openId"`
	PhoneForLogin string `json:"phoneForLogin"`
}

type PreviewData struct {
	Thumbnails []interface{} `json:"thumbnails"`
	Count      int           `json:"count"`
}

type Pan struct {
	OrgID    string `json:"orgId"`
	SpaceID  string `json:"spaceId"`
	DriveID  string `json:"driveId"`
	MemberID string `json:"memberId"`
	RootID   string `json:"rootId"`
}

func init() {
	gob.Register(UserInfo{})
	gob.Register(Pan{})
	gob.Register(ListResponse{})
	gob.Register(ItemResponse{})
}
