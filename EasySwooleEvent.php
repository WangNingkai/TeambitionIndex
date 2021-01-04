<?php


namespace EasySwoole\EasySwoole;


use EasySwoole\EasySwoole\AbstractInterface\Event;
use EasySwoole\EasySwoole\Swoole\EventRegister;

class EasySwooleEvent implements Event
{
    public static function initialize()
    {
        date_default_timezone_set('Asia/Shanghai');
    }

    public static function mainServerCreate(EventRegister $register)
    {
        // 配置同上别忘了添加要检视的目录
        $hotReloadOptions = new \EasySwoole\HotReload\HotReloadOptions;
        $hotReloadOptions->setMonitorFolder([EASYSWOOLE_ROOT . '/App']);
        $hotReload = new \EasySwoole\HotReload\HotReload($hotReloadOptions);
        $server = ServerManager::getInstance()->getSwooleServer();
        $hotReload->attachToServer($server);
    }
}
