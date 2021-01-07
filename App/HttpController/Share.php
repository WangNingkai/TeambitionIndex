<?php
/**
 * This file is part of the wangningkai's priviate source.
 * (c) wangningkai <i@ningkai.wang>
 */

namespace App\HttpController;


use App\Service\DB;
use App\Service\HashIds;
use App\Service\Teambition;
use EasySwoole\FastCache\Cache;

class Share extends Base
{
    public $actionWhiteList = ['view'];

    public function view()
    {
        $request = collect($this->request()->getRequestParam());
        $hash = $request->get('hash');
        $nodeId = $request->get('nodeID');
        $limit = $request->get('limit', 100);
        $offset = $request->get('offset', 0);

        $recordId = HashIds::getInstance()->decode($hash);
        $db = DB::getInstance()->getConnection();
        $rows = $db->select('records', '*', ['id' => $recordId]);
        $item = collect(current($rows));
        $rootId = $item->get('node_id');
        $userId = $item->get('user_id');
        $config = Cache::getInstance()->get($userId);
        $config = collect($config);
        $service = new Teambition($config->toArray());
        $nodeId = $nodeId ?: $rootId;
        try {
            $item = $service->getItem($nodeId);
        } catch (\Exception $e) {
            return $this->writeJson($e->getCode(), [], $e->getMessage());
        }
        $isFile = collect($item)->get('kind') === 'file';
        if ($isFile) {
            return $this->writeJson(200, $item, 'success');
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

    public function index()
    {
        $_id = $this->currentUserId;
        $db = DB::getInstance()->getConnection();
        $list = $db->select('records', '*', ['user_id' => $_id]);
        $data = collect($list)->toArray();
        return $this->writeJson(200, $data, 'success');
    }

    public function create()
    {
        $request = collect($this->json());
        $_id = $this->currentUserId;
        $node_id = $request->get('nodeId');
        $db = DB::getInstance()->getConnection();

        $name = $request->get('name');
        $db->insert('records', [
            'name' => $name,
            'user_id' => $_id,
            'node_id' => $node_id,
            'created_at' => time()
        ]);
        $record_id = $db->id();
        $hash = HashIds::getInstance()->encode($record_id);
        return $this->writeJson(200, $hash, 'success');
    }

    public function delete()
    {
        $record_id = $this->request()->getQueryParam('id');
        $db = DB::getInstance()->getConnection();
        $result = $db->delete('records', [
            'id' => $record_id
        ]);
        return $this->writeJson(200, $result->rowCount(), 'success');
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
