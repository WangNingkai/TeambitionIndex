<?php


namespace EasySwoole\EasySwoole;


use EasySwoole\EasySwoole\AbstractInterface\Event;
use EasySwoole\EasySwoole\Swoole\EventRegister;
use EasySwoole\Http\Request;
use EasySwoole\Http\Response;
use EasySwoole\HotReload\HotReload;
use EasySwoole\HotReload\HotReloadOptions;
use EasySwoole\FastCache\Cache;
use EasySwoole\FastCache\CacheProcessConfig;
use EasySwoole\FastCache\SyncData;
use EasySwoole\Utility\File;

class EasySwooleEvent implements Event
{
    public static function initialize()
    {
        date_default_timezone_set('Asia/Shanghai');
    }

    public static function mainServerCreate(EventRegister $register)
    {
        // 每隔5秒将数据存回文件
        Cache::getInstance()->setTickInterval(5 * 1000);//设置定时频率
        Cache::getInstance()->setOnTick(function (SyncData $SyncData, CacheProcessConfig $cacheProcessConfig) {
            $data = [
                'data' => $SyncData->getArray(),
                'queue' => $SyncData->getQueueArray(),
                'ttl' => $SyncData->getTtlKeys(),
                // queue支持
                'jobIds' => $SyncData->getJobIds(),
                'readyJob' => $SyncData->getReadyJob(),
                'reserveJob' => $SyncData->getReserveJob(),
                'delayJob' => $SyncData->getDelayJob(),
                'buryJob' => $SyncData->getBuryJob(),
            ];
            $path = EASYSWOOLE_TEMP_DIR . '/FastCacheData/' . $cacheProcessConfig->getProcessName();
            File::createFile($path, serialize($data));
        });

        // 启动时将存回的文件重新写入
        Cache::getInstance()->setOnStart(function (CacheProcessConfig $cacheProcessConfig) {
            $path = EASYSWOOLE_TEMP_DIR . '/FastCacheData/' . $cacheProcessConfig->getProcessName();
            if (is_file($path)) {
                $data = unserialize(file_get_contents($path));
                $syncData = new SyncData();
                $syncData->setArray($data['data']);
                $syncData->setQueueArray($data['queue']);
                $syncData->setTtlKeys(($data['ttl']));
                // queue支持
                $syncData->setJobIds($data['jobIds']);
                $syncData->setReadyJob($data['readyJob']);
                $syncData->setReserveJob($data['reserveJob']);
                $syncData->setDelayJob($data['delayJob']);
                $syncData->setBuryJob($data['buryJob']);
                return $syncData;
            }
        });

        // 在守护进程时,php easyswoole stop 时会调用,落地数据
        Cache::getInstance()->setOnShutdown(function (SyncData $SyncData, CacheProcessConfig $cacheProcessConfig) {
            $data = [
                'data' => $SyncData->getArray(),
                'queue' => $SyncData->getQueueArray(),
                'ttl' => $SyncData->getTtlKeys(),
                // queue支持
                'jobIds' => $SyncData->getJobIds(),
                'readyJob' => $SyncData->getReadyJob(),
                'reserveJob' => $SyncData->getReserveJob(),
                'delayJob' => $SyncData->getDelayJob(),
                'buryJob' => $SyncData->getBuryJob(),
            ];
            $path = EASYSWOOLE_TEMP_DIR . '/FastCacheData/' . $cacheProcessConfig->getProcessName();
            File::createFile($path, serialize($data));
        });
        Cache::getInstance()->setTempDir(EASYSWOOLE_TEMP_DIR)->attachToServer(ServerManager::getInstance()->getSwooleServer());
        if (Core::getInstance()->runMode() === 'dev') {
            // 配置同上别忘了添加要检视的目录
            $hotReloadOptions = new HotReloadOptions;
            $hotReloadOptions->setMonitorFolder([EASYSWOOLE_ROOT . '/App']);
            $hotReload = new HotReload($hotReloadOptions);
            $hotReload->attachToServer(ServerManager::getInstance()->getSwooleServer());
        }
    }

    public static function onRequest(Request $request, Response $response): bool
    {
    }

    public static function afterRequest(Request $request, Response $response): void
    {

    }
}
