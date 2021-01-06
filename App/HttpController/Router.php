<?php


namespace App\HttpController;


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

        $routeCollector->get('/', function (Request $request, Response $response) {
            $file = EASYSWOOLE_ROOT . '/Static/dist/index.html';
            $response->write(file_get_contents($file));
        });
    }
}
