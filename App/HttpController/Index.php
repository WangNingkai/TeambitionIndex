<?php


namespace App\HttpController;


use App\Service\Teambition;
use App\Service\TeambitionAuth;
use App\Service\TeambitionClient;
use EasySwoole\FastCache\Cache;
use EasySwoole\HttpAnnotation\AnnotationController;
use EasySwoole\HttpAnnotation\AnnotationTag\Method;
use Tightenco\Collect\Support\Arr;

class Index extends AnnotationController
{
    /**
     * 登录缓存
     * @Method(allow={POST})
     * @throws \Exception
     */
    public function login()
    {
        $request = $this->json();
        $phone = Arr::get($request, 'phone');
        $password = Arr::get($request, 'password');
        try {
            $login = TeambitionAuth::login($phone, $password);
        } catch (\Exception $e) {
            return $this->writeJson(500, [], $e->getMessage());
        }

        $cookie = Arr::get($login, 'cookie');
        $user = Arr::get($login, 'user');
        $client = new Teambition($cookie);
        $pan_params = $client->getPanConfig();
        if ($client->getErrCode() !== 0) {
            return $this->writeJson(500, [], $client->getErrMsg());
        }
        $config = array_merge($login, $pan_params);
        Cache::getInstance()->set($user['_id'], $config, 3600 * 72);
        return $this->writeJson(200, $user, 'success');
    }

    /**
     * 获取列表
     * @Method(allow={GET})
     * @throws \Exception
     */
    public function fetchList()
    {
        $request = $this->request();
        $_id = $request->getRequestParam('_id');
        $nodeId = $request->getRequestParam('nodeId');
        $config = Cache::getInstance()->get($_id);
        $client = new TeambitionClient($config);
        $service = new Teambition($client->cookie);
        $resp = $service->getItemList($client->orgId, $client->spaceId, $client->driveId, $nodeId ?: $client->rootId);
        $this->writeJson(200, $resp, 'success');
    }

    /**
     * 获取资源详情
     * @Method(allow={GET})
     * @throws \Exception
     */
    public function fetchItem()
    {
        $request = $this->request();
        $_id = $request->getRequestParam('_id');
        $nodeId = $request->getRequestParam('nodeId');
        $config = Cache::getInstance()->get($_id);
        $client = new TeambitionClient($config);
        $service = new Teambition($client->cookie);
        $resp = $service->getItem($client->orgId, $client->spaceId, $client->driveId, $nodeId ?: $client->rootId);
        $this->writeJson(200, $resp, 'success');
    }

    protected function actionNotFound(?string $action)
    {
        $this->writeJson(404, [], 'NotFound');
    }
}
