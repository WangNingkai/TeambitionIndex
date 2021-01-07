<?php
/**
 * This file is part of the wangningkai's priviate source.
 * (c) wangningkai <i@ningkai.wang>
 */

namespace App\Service;


use EasySwoole\Component\Singleton;
use EasySwoole\Jwt\Jwt;
use EasySwoole\Log\Logger;
use EasySwoole\Utility\File;
use EasySwoole\Utility\Random;

class App
{
    use Singleton;

    /**
     * @return $this
     */
    public function initialize(): App
    {
        $file = EASYSWOOLE_ROOT . '/Storage/Data/secret.txt';
        if (!file_exists($file)) {
            File::createFile($file, Random::character(32), true);
        }
        DB::getInstance()->initialize();
        HashIds::getInstance()->initialize();
        return $this;
    }

    /**
     * @return false|string
     */
    public function getSecret()
    {
        $file = EASYSWOOLE_ROOT . '/Storage/Data/secret.txt';
        if (!file_exists($file)) {
            $str = Random::character(32);
            File::createFile($file, $str, true);
        }
        return file_get_contents($file);
    }

    /**
     * @param array $user
     * @return string
     */
    public function getJwtToken($user = []): string
    {
        $jwtObject = Jwt::getInstance()
            ->setSecretKey(App::getInstance()->getSecret()) // 秘钥
            ->publish();
        $jwtObject->setAlg('HMACSHA256'); // 加密方式
        $jwtObject->setAud($user['_id']); // 用户
        $jwtObject->setExp(time() + 3600); // 过期时间
        $jwtObject->setIat(time()); // 发布时间
        $jwtObject->setIss('Teambition-Index'); // 发行人
        $jwtObject->setJti(md5(time())); // jwt id 用于标识该jwt
        $jwtObject->setNbf(time() + 60 * 5); // 在此之前不可用
        $jwtObject->setSub('TOKEN'); // 主题
        $jwtObject->setData($user); // 额外信息
        return $jwtObject->__toString();
    }

    /**
     * @param string $token
     * @return mixed
     */
    public function decodeJwtToken($token = '')
    {
        try {
            $jwtObject = Jwt::getInstance()->setSecretKey($this->getSecret())->decode($token);
            $status = $jwtObject->getStatus();

            $user_id = false;
            if ($status === 1) {
                $user_id = $jwtObject->getAud();
            }

            return $user_id;
        } catch (\EasySwoole\Jwt\Exception $e) {
            return false;
        }
    }
}
