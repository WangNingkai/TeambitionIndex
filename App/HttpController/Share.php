<?php
/**
 * This file is part of the wangningkai's priviate source.
 * (c) wangningkai <i@ningkai.wang>
 */

namespace App\HttpController;


use App\Service\App;
use App\Service\DB;
use EasySwoole\Http\AbstractInterface\Controller;

class Share extends Controller
{
    public function index()
    {
        $request = collect($this->json());
        $_id = $request->get('_id');
        $db = DB::getInstance()->getConnection();
        $list = $db->select('records', '*', ['user_id' => $_id]);
        $data = collect($list)->toArray();
        return $this->writeJson(200, $data, 'success');
    }

    public function create()
    {
        $request = collect($this->json());
        $_id = $request->get('_id');
        $node_id = $request->get('node_id');
        $db = DB::getInstance()->getConnection();

        $name = $request->get('name');
        $db->insert('records', [
            'name' => $name,
            'user_id' => $_id,
            'node_id' => $node_id,
        ]);
    }

    /**
     * 当控制器逻辑抛出异常时将调用该方法进行处理异常(框架默认已经处理了异常)可覆盖该方法,进行自定义的异常处理
     *
     * @param string|null $action
     * @return bool|null
     */
    public function onRequest(?string $action): ?bool
    {
        $token = $this->request()->getHeader('Token');
        $user_id = App::getInstance()->decodeJwtToken($token);
        if (!$user_id) {
            $this->writeJson(401, null, '请先登录');
            return true;
        }
        return parent::onRequest($action);
    }

    /**
     * 当请求方法未找到时,自动调用该方法,可自行覆盖该方法实现自己的逻辑
     *
     * @param string|null $action
     */
    protected function actionNotFound(?string $action)
    {
        $this->writeJson(404, [], 'NotFound');
    }

}
