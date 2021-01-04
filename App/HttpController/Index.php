<?php


namespace App\HttpController;


use App\Service\Teambition;
use EasySwoole\FastCache\Cache;
use EasySwoole\HttpAnnotation\AnnotationController;
use EasySwoole\HttpAnnotation\AnnotationTag\Method;
use Tightenco\Collect\Support\Arr;

class Index extends AnnotationController
{
    /**
     * @Method(allow={POST})
     * @throws \Exception
     */
    public function login()
    {
        $request = $this->json();
        $phone = Arr::get($request, 'phone');
        $password = Arr::get($request, 'password');
        $client = new Teambition();
        $login = $client->login($phone, $password);
        if (blank($login)) {
            $this->writeJson(500, [], $client->getErrMsg());
        }
        $cookie = $login['cookie'];
        $user = $login['user'];
        $pan_params = $client->getPanConfig($cookie);
        $config = array_merge($login, $pan_params);
        Cache::getInstance()->set($user['_id'], $user, 3600);
        Cache::getInstance()->set('cookie:' . $user['_id'], $cookie, 3600);
        Cache::getInstance()->set('pan:' . $user['_id'], $pan_params, 3600);
        $this->writeJson(200, $config, 'success');
    }

    /**
     * @Method(allow={GET})
     * @throws \Exception
     */
    public function fetchList()
    {
        $request = $this->request();
        $_id = $request->getRequestParam('_id');
        /*$config = json_decode(Cache::getInstance()->get($_id), true);
        $cookie = Arr::get($config, 'cookie');
        $orgId = Arr::get($config, 'orgId');
        $spaceId = Arr::get($config, 'spaceId');
        $driveId = Arr::get($config, 'driveId');
        $rootId = Arr::get($config, 'rootId');
        $client = new Teambition();
        $resp = $client->getItemList($cookie, $orgId, $spaceId, $driveId, $rootId);*/
        $this->writeJson(200, Cache::getInstance()->get('cookie:'.$_id), 'success');
    }

    /**
     * @Method(allow={GET})
     * @throws \Exception
     */
    public function fetchItem()
    {
        $request = $this->request();
        $_id = $request->getRequestParam('_id');
        $config = json_decode(Cache::getInstance()->get($_id), true);
        $cookie = Arr::get($config, 'cookie');
        $orgId = Arr::get($config, 'orgId');
        $spaceId = Arr::get($config, 'spaceId');
        $driveId = Arr::get($config, 'driveId');
        $rootId = Arr::get($config, 'rootId');
        $client = new Teambition();
        $resp = $client->getItem($cookie, $orgId, $spaceId, $driveId, $rootId);
        $this->writeJson(200, $resp, 'success');
    }

    protected function actionNotFound(?string $action)
    {
        $this->writeJson(404, [], 'NotFound');
    }
}
