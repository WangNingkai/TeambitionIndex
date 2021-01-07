<?php
/**
 * This file is part of the wangningkai's priviate source.
 * (c) wangningkai <i@ningkai.wang>
 */

namespace App\HttpController;


use App\Service\App;
use EasySwoole\Http\AbstractInterface\Controller;

class Base extends Controller
{
    public $actionWhiteList = [];

    protected $currentUserId;

    /**
     * 当控制器逻辑抛出异常时将调用该方法进行处理异常(框架默认已经处理了异常)可覆盖该方法,进行自定义的异常处理
     *
     * @param string|null $action
     * @return bool|null
     */
    public function onRequest(?string $action): ?bool
    {
        if (!in_array($action, $this->actionWhiteList, false)) {
            $token = current($this->request()->getHeader('token'));
            $user_id = App::getInstance()->decodeJwtToken($token);
            if (!$user_id) {
                $this->writeJson(401, [], '登录失效');
                return false;
            }
            $this->currentUserId = $user_id;
        }
        return parent::onRequest($action);
    }
}
