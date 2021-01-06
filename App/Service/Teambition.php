<?php
/**
 * This file is part of the wangningkai's priviate source.
 * (c) wangningkai <i@ningkai.wang>
 */

namespace App\Service;


use EasySwoole\HttpClient\HttpClient;

class Teambition
{
    /**
     * @var $err_code
     */
    private $err_code;
    /**
     * @var $err_msg
     */
    private $err_msg;
    /**
     * @var string
     */
    public $cookie = [];
    /**
     * @var string
     */
    public $user = [];
    /**
     * @var string
     */
    public $orgId = '';
    /**
     * @var string
     */
    public $spaceId = '';
    /**
     * @var string
     */
    public $driveId = '';
    /**
     * @var string
     */
    public $rootId = '';

    /**
     * Teambition constructor.
     * @param $options
     */
    public function __construct($options = [])
    {
        foreach ($options as $k => $v) {
            if (property_exists($this, $k)) {
                $this->$k = $v;
            }
        }
    }

    /**
     * 获取OrgId
     * @return array|mixed
     * @throws \Exception
     */
    public function getOrgId()
    {
        $url = 'https://www.teambition.com/api/organizations/personal';
        $client = $this->_initClient($url);
        $resp = $client->get();
        $this->err_code = $resp->getStatusCode();
        $body = $resp->getBody();
        $body = is_json($body) ? json_decode($body, true) : $body;
        if ($resp && $this->err_code < 400) {
            $_decode = collect($body);
            if ($_decode->has('_id') && $_decode->has('_creatorId')) {
                return $_decode->toArray();
            }
        }
        $this->err_msg = collect($body)->get('message');
        throw new \RuntimeException($this->err_msg, $this->err_code);
    }

    /**
     * 获取DriveId
     * @param $orgId
     * @return array|mixed
     * @throws \Exception
     */
    public function getDriveId($orgId)
    {
        $url = 'https://pan.teambition.com/pan/api/orgs/' . $orgId;
        $client = $this->_initClient($url);
        $resp = $client->get();
        $this->err_code = $resp->getStatusCode();
        $body = $resp->getBody();
        $body = is_json($body) ? json_decode($body, true) : $body;
        if ($resp && $this->err_code < 400) {
            $_decode = collect($body);
            $_decode = $_decode->toArray();
            if (isset($_decode['data']['driveId'])) {
                return $_decode;
            }
        }
        $this->err_msg = collect($body)->get('message');
        throw new \RuntimeException($this->err_msg, $this->err_code);
    }

    /**
     * 获取SpaceId
     * @param $orgId
     * @param $memberId
     * @return array|mixed
     * @throws \Exception
     */
    public function getSpaceId($orgId, $memberId)
    {
        $url = 'https://pan.teambition.com/pan/api/spaces?';
        $client = $this->_initClient($url);
        $client->setQuery([
            'orgId' => $orgId,
            'memberId' => $memberId
        ]);
        $resp = $client->get();
        $this->err_code = $resp->getStatusCode();
        $body = $resp->getBody();
        $body = is_json($body) ? json_decode($body, true) : $body;
        if ($resp && $this->err_code < 400) {
            $_decode = collect($body);
            $_decode = $_decode->toArray();
            if (isset($_decode['0']['spaceId'], $_decode[0]['rootId'])) {
                return $_decode;
            }
        }
        $this->err_msg = collect($body)->get('message');
        throw new \RuntimeException($this->err_msg, $this->err_code);
    }

    /**
     * 获取资源列表
     * @param $nodeId
     * @param int $limit
     * @param int $offset
     * @return array|mixed
     * @throws \Exception
     */
    public function getItemList($nodeId, $limit = 100, $offset = 0)
    {
        $url = 'https://pan.teambition.com/pan/api/nodes?';
        $params = [
            'orgId' => $this->orgId,
            'spaceId' => $this->spaceId,
            'driveId' => $this->driveId,
            'parentId' => $nodeId,
            'offset' => $offset,
            'limit' => $limit,
            'orderBy' => 'updateTime',
            'orderDirection' => 'desc'
        ];
        $client = $this->_initClient($url);
        $client->setQuery($params);
        $resp = $client->get();
        $this->err_code = $resp->getStatusCode();
        $body = $resp->getBody();
        $body = is_json($body) ? json_decode($body, true) : $body;
        if ($resp && $this->err_code < 400) {
            $_decode = collect($body);
            return $_decode->toArray();
        }
        $this->err_msg = collect($body)->get('message');
        throw new \RuntimeException($this->err_msg, $this->err_code);
    }

    /**
     * 获取资源详情
     * @param $nodeId
     * @return array|mixed
     * @throws \Exception
     */
    public function getItem($nodeId)
    {
        $url = "https://pan.teambition.com/pan/api/nodes/{$nodeId}?";
        $params = [
            'orgId' => $this->orgId,
            'spaceId' => $this->spaceId,
            'driveId' => $this->driveId,
        ];
        $client = $this->_initClient($url);
        $client->setQuery($params);
        $resp = $client->get();
        $this->err_code = $resp->getStatusCode();
        $body = $resp->getBody();
        $body = is_json($body) ? json_decode($body, true) : $body;
        if ($resp && $this->err_code < 400) {
            $_decode = collect($body);
            return $_decode->toArray();
        }
        $this->err_msg = collect($body)->get('message');
        throw new \RuntimeException($this->err_msg, $this->err_code);
    }

    /**
     * 获取网盘的相关配置
     * @return array
     * @throws \Exception
     */
    public function getPanConfig()
    {
        $config = [
            'orgId' => '',
            'memberId' => '',
            'spaceId' => '',
            'driveId' => '',
            'rootId' => '',
        ];
        $org = $this->getOrgId();
        if ($org) {
            $config['orgId'] = $org['_id'];
            $config['memberId'] = $org['_creatorId'];
            $space = $this->getSpaceId($config['orgId'], $config['memberId']);
            if ($space) {
                $config['spaceId'] = $space[0]['spaceId'];
                $config['rootId'] = $space[0]['rootId'];
                $drive = $this->getDriveId($config['orgId']);
                if ($drive) {
                    $config['driveId'] = $drive['data']['driveId'];
                }
            }
        }
        return $config;
    }

    private function _initClient($url)
    {
        $ua = 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/87.0.4280.66 Safari/537.36';
        $client = new HttpClient();
        $client->setUrl($url);
        $client->setHeader('User-Agent', $ua);
        if (!blank($this->cookie)) {
            $client->addCookies($this->cookie);
        }

        return $client;
    }

    public function getErrMsg()
    {
        return $this->err_msg;
    }

    public function getErrCode()
    {
        return $this->err_code;
    }
}
