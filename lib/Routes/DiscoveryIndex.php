<?php

namespace LuckyConsultation\Routes;

use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;

use LuckyConsultation\LuckyConsultationTrait;
use LuckyConsultation\LuckyConsultationController;

class DiscoveryIndex extends LuckyConsultationController
{
    use LuckyConsultationTrait;

    public function __invoke(Request $request, Response $response, $args)
    {
        $routes = $this->container->get(\Slim\App::class)->getRouteCollector()->getRoutes();

        foreach ($routes as $id => $route) {
            $data[] = [
                'type' => 'slim-routes',
                'id'   => $id,
                'attributes' => [
                    'methods' => $route->getMethods(),
                    'pattern' => $route->getPattern()
                ]
            ];
        }
        return $this->createResponse(['data' => $data], $response);
    }
}
