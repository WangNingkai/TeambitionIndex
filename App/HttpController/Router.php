<?php


namespace App\HttpController;


use App\Service\Teambition;
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

        $routeCollector->get('/t', function (Request $request, Response $response) {
            $service = new Teambition([]);
            try {
                $r = $service->getOrgId();
            } catch (\Exception $e) {
                $response->write($e->getMessage());
                return true;
            }

            $response->write($r);
            return true;
        });
    }
}
