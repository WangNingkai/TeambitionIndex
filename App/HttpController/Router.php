<?php


namespace App\HttpController;


use App\Service\DB;
use EasySwoole\Http\AbstractInterface\AbstractRouter;
use EasySwoole\Http\Request;
use EasySwoole\Http\Response;
use FastRoute\RouteCollector;

class Router extends AbstractRouter
{
    function initialize(RouteCollector $routeCollector)
    {
        $this->setGlobalMode(true);
        $routeCollector->post('/api/login', '/Index/login');
        $routeCollector->post('/api/nodes', '/Index/fetchList');
        $routeCollector->post('/api/node', '/Index/fetchItem');

        $routeCollector->post('/api/share', '/Share/create');
        $routeCollector->get('/api/share', '/Share/index');
        $routeCollector->delete('/api/share/{id:\d+}', '/Share/delete');
        $routeCollector->get('/api/share/{hash}', '/Share/view');

        $routeCollector->get('/', function (Request $request, Response $response) {
            $db = DB::getInstance()->getConnection();

            $response->write(json_encode($db->error()));
            /*$file = EASYSWOOLE_ROOT . '/Static/dist/index.html';
            $response->write(file_get_contents($file));*/
        });
    }
}
