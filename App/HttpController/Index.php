<?php


namespace App\HttpController;


use App\Service\Teambition;
use EasySwoole\Http\AbstractInterface\Controller;
use EasySwoole\HttpClient\HttpClient;

class Index extends Controller
{
    public function index()
    {
        $file = EASYSWOOLE_ROOT . '/vendor/easyswoole/easyswoole/src/Resource/Http/welcome.html';
        if (!is_file($file)) {
            $file = EASYSWOOLE_ROOT . '/src/Resource/Http/welcome.html';
        }
        $this->response()->write(file_get_contents($file));
    }

    public function test()
    {
        $client = new Teambition();
//        $cookie = $client->login('手机号', '密码');
        $cookie = [
            "TEAMBITION_SESSIONID" => "eyJhdXRoVXBkYXRlZCI6MTYwNTU3MjgyNTI4OSwibG9naW5Gcm9tIjoidGVhbWJpdGlvbiIsInVpZCI6IjVmYjMxOGQ5NzQxNjY3ODczMmIzOGYzZCIsInVzZXIiOnsiX2lkIjoiNWZiMzE4ZDk3NDE2Njc4NzMyYjM4ZjNkIiwibmFtZSI6IueOi WugeWHryIsImVtYWlsIjoiYWNjb3VudHNfNWZiMzE4ZDk3YzIzOGQwMDE2ZmE2ZWJiQG1haWwudGVhbWJpdGlvbi5jb20iLCJhdmF0YXJVcmwiOiJodHRwczovL3Rjcy50ZWFtYml0aW9uLm5ldC90aHVtYm5haWwvMTExejdiM2I2NmI1ZjA5MDUyYmI0Mjc1NjlmYTU1OWI5OTNmL3cvMjAwL2gvMjAwIiwicmVnaW9uIjoiY24iLCJsYW5nIjoiIiwiaXNSb2JvdCI6ZmFsc2UsIm9wZW5JZCI6IiIsInBob25lRm9yTG9naW4iOiIxODY2MTIyNTk5NSJ9fQ==",
            "TEAMBITION_SESSIONID.sig" => "r-3WERdqZyZxssG0L1kJNuxxhyE"
        ];
        $orgId = '5fb318d93d978eae1b5c30b2';
        $driveId = '51399';
        $memberId = '5fb318d97416678732b38f3d';
        $rootId = 'c59e6a1e93703c4ae6fd2eda23b75a74';
        $spaceId = 'cfbe76dfdb1cdc9e';
        $ret = $client->getItemList($cookie, $orgId, $spaceId, $driveId, $rootId);
//        $ret = $client->getItem($cookie, $orgId, $spaceId, $driveId, $rootId);
        $this->writeJson(200, $ret, 'success');
    }

    protected function actionNotFound(?string $action)
    {
        $this->response()->withStatus(404);
        $file = EASYSWOOLE_ROOT . '/vendor/easyswoole/easyswoole/src/Resource/Http/404.html';
        if (!is_file($file)) {
            $file = EASYSWOOLE_ROOT . '/src/Resource/Http/404.html';
        }
        $this->response()->write(file_get_contents($file));
    }
}
