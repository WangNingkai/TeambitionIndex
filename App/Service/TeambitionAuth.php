<?php
/**
 * This file is part of the wangningkai's priviate source.
 * (c) wangningkai <i@ningkai.wang>
 */

namespace App\Service;


use EasySwoole\HttpClient\HttpClient;

class TeambitionAuth
{
    /**
     * 获取登录Token
     * @return mixed|string
     * @throws \Exception
     */
    private static function getLoginToken()
    {
        $url = 'https://account.teambition.com/login/password';
        $client = new HttpClient();
        $client->setUrl($url);
        $client->setHeader('User-Agent', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/87.0.4280.66 Safari/537.36');
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
    public static function login($username = '', $password = '')
    {
        $token = self::getLoginToken();

        $data = json_encode([
            'phone' => $username,
            'password' => $password,
            'token' => $token,
            'client_id' => '90727510-5e9f-11e6-bf41-15ed35b6cc41',
            'response_type' => 'session'
        ], JSON_THROW_ON_ERROR);

        $url = 'https://account.teambition.com/api/login/phone';
        $client = new HttpClient();
        $client->setUrl($url);
        $client->setHeader('User-Agent', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/87.0.4280.66 Safari/537.36');
        $resp = $client->postJson($data);
        $err_code = $resp->getErrCode();
        if ($resp && $err_code === 0) {
            return ['cookie' => $resp->getCookies(), 'user' => json_decode($resp->getBody(), true)['user']];
        }
        throw new \Exception($resp->getErrMsg(), $err_code);
    }
}
