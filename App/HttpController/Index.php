<?php


namespace App\HttpController;


use App\Service\App;
use App\Service\Teambition;
use App\Service\TeambitionAuth;
use EasySwoole\FastCache\Cache;

class Index extends Base
{
    public $actionWhiteList = ['login'];

    /**
     * 登录缓存
     * @throws \Exception
     */
    public function login()
    {
        $request = collect($this->json());
        $phone = $request->get('phone');
        $password = $request->get('password');
        try {
            $login = TeambitionAuth::login($phone, $password);
        } catch (\Exception $e) {
            return $this->writeJson(500, [], $e->getMessage());
        }

        $login = collect($login);
        $cookie = $login->get('cookie');
        $user = $login->get('user');
        $client = new Teambition(['cookie' => $cookie]);
        try {
            $pan_params = $client->getPanConfig();
        } catch (\Exception $e) {
            return $this->writeJson($e->getCode(), [], $e->getMessage());
        }
        $config = array_merge($login->toArray(), $pan_params);
        Cache::getInstance()->set($user['_id'], $config, 86400 * 7);

        $token = App::getInstance()->getJwtToken($user);

        return $this->writeJson(200, compact('user', 'token'), 'success');
    }

    /**
     * 获取列表
     * @throws \Exception
     */
    public function fetchList()
    {
        $request = collect($this->json());
        $_id = $this->currentUserId;
        $nodeId = $request->get('nodeId');
        $limit = $request->get('limit', 100);
        $offset = $request->get('offset', 0);
        $config = Cache::getInstance()->get($_id);
        $config = collect($config);
        $service = new Teambition($config->toArray());
        $rootId = $config->get('rootId');
        $nodeId = $nodeId ?: $rootId;
        try {
            $item = $service->getItem($nodeId);

        } catch (\Exception $e) {
            return $this->writeJson($e->getCode(), [], $e->getMessage());
        }
        try {
            $list = $service->getItemList($nodeId, $limit, $offset);
        } catch (\Exception $e) {
            return $this->writeJson($e->getCode(), [], $e->getMessage());
        }

        $data = collect($list);

        $rootId = $config->get('rootId');
        $result = [
            'limit' => $data->get('limit'),
            'offset' => $data->get('offset'),
            'totalCount' => $data->get('totalCount'),
            'list' => $data->get('data'),
            'item' => $item,
            'isRoot' => (int)($rootId === $nodeId)
        ];
        return $this->writeJson(200, $result, 'success');
    }

    /**
     * 获取资源详情
     * @throws \Exception
     */
    public function fetchItem()
    {
        $request = collect($this->json());
        $_id = $this->currentUserId;
        $nodeId = $request->get('nodeId');
        $config = Cache::getInstance()->get($_id);
        $config = collect($config);
        $service = new Teambition($config->toArray());
        $rootId = $config->get('rootId');
        try {
            $resp = $service->getItem($nodeId ?: $rootId);
        } catch (\Exception $e) {
            return $this->writeJson($e->getCode(), [], $e->getMessage());
        }
        return $this->writeJson(200, $resp, 'success');
    }

    /**
     * 当控制器逻辑抛出异常时将调用该方法进行处理异常(框架默认已经处理了异常)可覆盖该方法,进行自定义的异常处理
     *
     * @param string|null $action
     * @return bool|null
     */
    public function onRequest(?string $action): ?bool
    {
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
