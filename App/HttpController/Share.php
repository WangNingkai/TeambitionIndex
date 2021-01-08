<?php
/**
 * This file is part of the wangningkai's priviate source.
 * (c) wangningkai <i@ningkai.wang>
 */

namespace App\HttpController;


use App\Service\App;
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
        $recordId = HashIds::getInstance()->decode($hash);
        if (null === $recordId) {
            return $this->writeJson(404, [], 'notFound');
        }
        $db = DB::getInstance()->getConnection();
        $rows = $db->select('records', '*', ['id' => $recordId]);
        $item = collect(current($rows));
        $nodeId = $item->get('node_id');
        $userId = $item->get('user_id');
        $config = Cache::getInstance()->get($userId);
        $config = collect($config);
        $service = new Teambition($config->toArray());
        try {
            $item = $service->getItem($nodeId);
        } catch (\Exception $e) {
            return $this->writeJson($e->getCode(), [], $e->getMessage());
        }
        $isFile = collect($item)->get('kind') === 'file';
        if (!$isFile) {
            return $this->writeJson(404, [], 'notFound');
        }
        return $this->writeJson(200, $item, 'success');
    }

    public function index()
    {
        $request = collect($this->request()->getRequestParam());
        $page = $request->get('page', 1);
        $perPage = $request->get('perPage', 20);
        $_id = $this->currentUserId;
        $db = DB::getInstance()->getConnection();
        $offset = max(0, ($page - 1) * $perPage);
        $limit = $perPage;
        $list = $db->select('records', '*', ['user_id' => $_id, 'LIMIT' => [$offset, $limit]]);
        $list = collect($list)->map(function($item) {
            $item['hash'] = HashIds::getInstance()->encode($item['id']);
            return $item;
        })->all();
        $totalCount = $db->count('records', '*', ['user_id' => $_id,]);
        if ($limit < 1) {
            $totalPage = $totalCount > 0 ? 1 : 0;
        } else {
            $totalCount = $totalCount < 0 ? 0 : (int)$totalCount;
            $totalPage = (int)(($totalCount + $limit - 1) / $limit);
        }
        $result = [
            'currentPage' => $page,
            'perPage' => $perPage,
            'totalCount' => $totalCount,
            'totalPage' => $totalPage,
            'list' => $list
        ];
        return $this->writeJson(200, $result, 'success');
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
