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
     * @var $cookie
     */
    private $cookie;

    /**
     * Teambition constructor.
     * @param $cookie
     */
    public function __construct($cookie)
    {
        $this->cookie = $cookie;
    }

    /**
     * 获取OrgId
     * @return array|mixed
     * @throws \EasySwoole\HttpClient\Exception\InvalidUrl
     */
    private function getOrgId()
    {
        $url = 'https://www.teambition.com/api/organizations/personal';
        $client = $this->_initClient($url);
        $resp = $client->get();
        $err_code = $resp->getErrCode();
        $this->err_msg = $resp->getErrMsg();
        $this->err_code = $err_code;
        if ($resp && $err_code === 0) {
            $body = $resp->getBody();
            $_decode = json_decode($body, true);
            if (isset($_decode['_id'], $_decode['_creatorId'])) {
                return $_decode;
            }
        }
        return [];
    }

    /**
     * 获取DriveId
     * @param $orgId
     * @return array|mixed
     * @throws \EasySwoole\HttpClient\Exception\InvalidUrl
     */
    private function getDriveId($orgId)
    {
        $url = 'https://pan.teambition.com/pan/api/orgs/' . $orgId;
        $client = $this->_initClient($url);
        $resp = $client->get();
        $err_code = $resp->getErrCode();
        $this->err_msg = $resp->getErrMsg();
        $this->err_code = $err_code;
        if ($resp && $err_code === 0) {
            $body = $resp->getBody();
            $_decode = json_decode($body, true);
            if (isset($_decode['data']['driveId'])) {
                return $_decode;
            }
        }
        return [];
    }

    /**
     * 获取SpaceId
     * @param $orgId
     * @param $memberId
     * @return array|mixed
     * @throws \EasySwoole\HttpClient\Exception\InvalidUrl
     */
    private function getSpaceId($orgId, $memberId)
    {
        $url = 'https://pan.teambition.com/pan/api/spaces?';
        $client = $this->_initClient($url);
        $client->setQuery([
            'orgId' => $orgId,
            'memberId' => $memberId
        ]);
        $resp = $client->get();
        $err_code = $resp->getErrCode();
        $this->err_msg = $resp->getErrMsg();
        $this->err_code = $err_code;
        if ($resp && $err_code === 0) {
            $body = $resp->getBody();
            $_decode = json_decode($body, true);
            if (isset($_decode['0']['spaceId'], $_decode[0]['rootId'])) {
                return $_decode;
            }
        }

        return [];
    }

    /**
     * 获取资源列表
     * @param $orgId
     * @param $spaceId
     * @param $driveId
     * @param $parentId
     * @param int $limit
     * @param int $offset
     * @return array|mixed
     * @throws \EasySwoole\HttpClient\Exception\InvalidUrl
     */
    public function getItemList($orgId, $spaceId, $driveId, $parentId, $limit = 100, $offset = 0)
    {
        $url = 'https://pan.teambition.com/pan/api/nodes?';
        $params = [
            'orgId' => $orgId,
            'spaceId' => $spaceId,
            'driveId' => $driveId,
            'parentId' => $parentId,
            'offset' => $offset,
            'limit' => $limit,
            'orderBy' => 'updateTime',
            'orderDirection' => 'desc'
        ];
        $client = $this->_initClient($url);
        $client->setQuery($params);
        $resp = $client->get();
        $err_code = $resp->getErrCode();
        $this->err_msg = $resp->getErrMsg();
        $this->err_code = $err_code;
        if ($resp && $err_code === 0) {
            $body = $resp->getBody();
            $_decode = json_decode($body, true);
            return $_decode;
        }
        return [];


    }

    /**
     * 获取资源详情
     * @param $orgId
     * @param $spaceId
     * @param $driveId
     * @param $parentId
     * @return array|mixed
     * @throws \EasySwoole\HttpClient\Exception\InvalidUrl
     */
    public function getItem($orgId, $spaceId, $driveId, $parentId)
    {
        $url = "https://pan.teambition.com/pan/api/nodes/{$parentId}?";
        $params = [
            'orgId' => $orgId,
            'spaceId' => $spaceId,
            'driveId' => $driveId,
        ];
        $client = $this->_initClient($url);
        $client->setQuery($params);
        $resp = $client->get();
        $err_code = $resp->getErrCode();
        $this->err_msg = $resp->getErrMsg();
        $this->err_code = $err_code;
        if ($resp && $err_code === 0) {
            $body = $resp->getBody();
            $_decode = json_decode($body, true);
            return $_decode;
        }
        return [];
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
