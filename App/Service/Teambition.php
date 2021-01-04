<?php
/**
 * This file is part of the wangningkai's priviate source.
 * (c) wangningkai <i@ningkai.wang>
 */

namespace App\Service;


use EasySwoole\HttpClient\HttpClient;
use EasySwoole\EasySwoole\Logger;

class Teambition
{

    public const UA = 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/87.0.4280.66 Safari/537.36';

    /**
     * 获取登录Token
     * @return mixed|string
     * @throws \EasySwoole\HttpClient\Exception\InvalidUrl
     */
    public function getLoginToken()
    {
        $url = 'https://account.teambition.com/login/password';
        $client = $this->_initClient($url);
        $resp = $client->get();
        $err_code = $resp->getErrCode();
        $token = '';
        if ($resp && $err_code === 0) {
            $body = $resp->getBody();
            if (preg_match('/"TOKEN":"([a-zA-Z0-9_\-\.]+)"/', $body, $match)) {
                $token = $match[1];
            }
        }
        return $token;
    }

    /**
     * 登录获取Cookie
     * @param string $username 账号
     * @param string $password 密码
     * @return array
     * @throws \Exception
     */
    public function login($username = '', $password = '')
    {
        $token = $this->getLoginToken();

        $data = json_encode([
            'phone' => $username,
            'password' => $password,
            'token' => $token,
            'client_id' => '90727510-5e9f-11e6-bf41-15ed35b6cc41',
            'response_type' => 'session'
        ], JSON_THROW_ON_ERROR);

        $url = 'https://account.teambition.com/api/login/phone';
        $client = $this->_initClient($url);
        $resp = $client->postJson($data);
        $err_code = $resp->getErrCode();

        $cookie = [];
        if ($resp && $err_code === 0) {
            return $resp->getCookies();
        }
        return $cookie;
    }

    /**
     * 获取OrgId
     * @param array $cookie
     * @return array|mixed
     * @throws \EasySwoole\HttpClient\Exception\InvalidUrl
     */
    public function getOrgId($cookie = [])
    {
        $url = 'https://www.teambition.com/api/organizations/personal';
        $client = $this->_initClient($url, $cookie);
        $resp = $client->get();
        $err_code = $resp->getErrCode();

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
     * @param $cookie
     * @param $orgId
     * @return array|mixed
     * @throws \EasySwoole\HttpClient\Exception\InvalidUrl
     */
    public function getDriveId($cookie, $orgId)
    {
        $url = 'https://pan.teambition.com/pan/api/orgs/' . $orgId;
        $client = $this->_initClient($url, $cookie);
        $resp = $client->get();
        $err_code = $resp->getErrCode();

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
     * @param $cookie
     * @param $orgId
     * @param $memberId
     * @return array|mixed
     * @throws \EasySwoole\HttpClient\Exception\InvalidUrl
     */
    public function getSpaceId($cookie, $orgId, $memberId)
    {
        $url = 'https://pan.teambition.com/pan/api/spaces?';
        $client = $this->_initClient($url, $cookie);
        $client->setQuery([
            'orgId' => $orgId,
            'memberId' => $memberId
        ]);
        $resp = $client->get();
        $err_code = $resp->getErrCode();

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
     * @param $cookie
     * @param $orgId
     * @param $spaceId
     * @param $driveId
     * @param $parentId
     * @param int $limit
     * @param int $offset
     * @return array|mixed
     * @throws \EasySwoole\HttpClient\Exception\InvalidUrl
     */
    public function getItemList($cookie, $orgId, $spaceId, $driveId, $parentId, $limit = 100, $offset = 0)
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
        $client = $this->_initClient($url, $cookie);
        $client->setQuery($params);
        $resp = $client->get();
        $err_code = $resp->getErrCode();

        if ($resp && $err_code === 0) {
            $body = $resp->getBody();
            $_decode = json_decode($body, true);
            return $_decode;
        }
        return [];


    }

    /**
     * 获取资源详情
     * @param $cookie
     * @param $orgId
     * @param $spaceId
     * @param $driveId
     * @param $parentId
     * @return array|mixed
     * @throws \EasySwoole\HttpClient\Exception\InvalidUrl
     */
    public function getItem($cookie, $orgId, $spaceId, $driveId, $parentId)
    {
        $url = "https://pan.teambition.com/pan/api/nodes/{$parentId}?";
        $params = [
            'orgId' => $orgId,
            'spaceId' => $spaceId,
            'driveId' => $driveId,
        ];
        $client = $this->_initClient($url, $cookie);
        $client->setQuery($params);
        $resp = $client->get();
        $err_code = $resp->getErrCode();

        if ($resp && $err_code === 0) {
            $body = $resp->getBody();
            $_decode = json_decode($body, true);
            return $_decode;
        }
        return [];
    }

    /**
     * 获取网盘的相关配置
     * @param array $cookie
     * @return array
     * @throws \Exception
     */
    public function getPanConfig($cookie = [])
    {
        $config = [];
        $org = $this->getOrgId($cookie);
        if ($org) {
            $config['orgId'] = $org['_id'];
            $config['memberId'] = $org['_creatorId'];
            $space = $this->getSpaceId($cookie, $config['orgId'], $config['memberId']);
            if ($space) {
                $config['spaceId'] = $space[0]['spaceId'];
                $config['rootId'] = $space[0]['rootId'];
                $drive = $this->getDriveId($cookie, $config['orgId']);
                if ($drive) {
                    $config['driveId'] = $drive['data']['driveId'];
                }
            }
        }
        return $config;
    }

    private function _initClient($url, $cookie = [])
    {
        $client = new HttpClient();
        $client->setUrl($url);
        $client->setHeader('User-Agent', self::UA);
        if (!blank($cookie)) {
            $client->addCookies($cookie);
        }

        return $client;
    }

}
