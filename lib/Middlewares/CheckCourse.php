<?php

namespace LuckyConsultation\Middlewares;

use Psr\Http\Message\RequestInterface as Request;
use Psr\Http\Server\RequestHandlerInterface as RequestHandler;
use LuckyConsultation\Errors\Error;
use Slim\Routing\RouteContext;

class CheckCourse
{
    // the container
    private $perms;


    /**
     * Der Konstruktor.
     *
     * @param callable $container the global slim container
     */
    public function __construct($perms)
    {
        $this->perms     = $perms;
    }

    /**
     * Checks, if the current user has the admin role
     *
     * @SuppressWarnings(PHPMD.Superglobals)
     */
    public function __invoke(Request $request, RequestHandler $handler)
    {
        $routeContext = RouteContext::fromRequest($request);
        $route = $routeContext->getRoute();
        $course_id = $route->getArgument('course_id');

        if (!$course_id || !$GLOBALS['perm']->have_studip_perm($this->perms, $course_id)) {
            throw new Error('Access Denied', 403);
        }

        return $handler->handle($request);

    }
}
