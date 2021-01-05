<?php


namespace App\HttpController;


use App\Service\Teambition;
use App\Service\TeambitionAuth;
use EasySwoole\FastCache\Cache;
use EasySwoole\HttpAnnotation\AnnotationController;
use EasySwoole\HttpAnnotation\AnnotationTag\Method;

class Index extends AnnotationController
{
    /**
     * 登录缓存
     * @Method(allow={POST})
     * @throws \Exception
     */
    public function login()
    {
        $request = collect($this->json());
        $phone = $request->get($request, 'phone');
        $password = $request->get($request, 'password');
        try {
            $login = TeambitionAuth::login($phone, $password);
        } catch (\Exception $e) {
            return $this->writeJson(500, [], $e->getMessage());
        }
        $login = collect($login);
        $cookie = $login->get('cookie');
        $user = $login->get('user');
        $client = new Teambition(['cookie' => $cookie]);
        $pan_params = $client->getPanConfig();
        if ($client->getErrCode() !== 0) {
            return $this->writeJson(500, [], $client->getErrMsg());
        }
        $config = array_merge($login->toArray(), $pan_params);
        Cache::getInstance()->set($user['_id'], $config, 3600 * 72);
        return $this->writeJson(200, $user, 'success');
    }

    /**
     * 获取列表
     * @Method(allow={POST})
     * @throws \Exception
     */
    public function fetchList()
    {
        $request = collect($this->json());
        $_id = $request->get('_id');
        $nodeId = $request->get('nodeId');
        $limit = $request->get('limit', 100);
        $offset = $request->get('offset', 0);
        $config = Cache::getInstance()->get($_id);
        $config = collect($config);
        $service = new Teambition($config->toArray());
        $rootId = $config->get('rootId');
        $nodeId = $nodeId ?: $rootId;
        $list = $service->getItemList($nodeId, $limit, $offset);
        $item = $service->getItem($nodeId);
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
     * @Method(allow={POST})
     * @throws \Exception
     */
    public function fetchItem()
    {
        $request = collect($this->json());
        $_id = $request->get('_id');
        $nodeId = $request->get('nodeId');
        $config = Cache::getInstance()->get($_id);
        $config = collect($config);
        $service = new Teambition($config->toArray());
        $rootId = $config->get('rootId');
        $resp = $service->getItem($nodeId ?: $rootId);
        return $this->writeJson(200, $resp, 'success');
    }

    protected function actionNotFound(?string $action)
    {
        $this->writeJson(404, [], 'NotFound');
    }
}
