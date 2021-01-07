<?php
/**
 * This file is part of the wangningkai's priviate source.
 * (c) wangningkai <i@ningkai.wang>
 */

namespace App\Service;


use EasySwoole\Component\Singleton;
use Hashids\Hashids as Hash;

class HashIds
{
    use Singleton;

    /**
     * @var int
     */
    public static $length = 8;


    /**
     * @var \Hashids\Hashids
     */
    private $hashids;

    /**
     * @return HashIds
     */
    public function initialize(): HashIds
    {
        $secret = App::getInstance()->getSecret();
        $this->hashids = new Hash($secret, self::$length);
        return $this;
    }

    /**
     * 加密
     *
     * @param mixed ...$numbers
     * @return string
     */
    public function encode(...$numbers): string
    {
        return $this->hashids->encode(...$numbers);
    }

    /**
     * 解密
     *
     * @param string $hash
     * @return mixed
     * @throws \Exception
     */
    public function decode(string $hash)
    {
        $data = $this->hashids->decode($hash);
        if (empty($data) || !is_array($data)) {
            return null;
        }

        return count($data) === 1 ? $data[0] : $data;
    }


}
