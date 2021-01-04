<?php


namespace App\HttpController;


use App\Service\Teambition;
use EasySwoole\FastCache\Cache;
use EasySwoole\HttpAnnotation\AnnotationController;
use EasySwoole\HttpAnnotation\AnnotationTag\Method;
use EasySwoole\HttpAnnotation\AnnotationTag\Param;
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
        $config = [
            'cookie' => $cookie,
            'user' => $user,
            'pan' => $pan_params
        ];
        Cache::getInstance()->set($user['_id'], json_encode($config));
        $this->writeJson(200, $config, 'success');
    }

    protected function actionNotFound(?string $action)
    {
        $this->writeJson(404, [], 'notfound');
    }
}
