<?php

namespace LuckyConsultation\Middlewares;

use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Message\ResponseInterface as Response;
use LuckyConsultation\Errors\Error;

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
     * @param \Psr\Http\Message\ServerRequestInterface $request  das
     *                                                           PSR-7 Request-Objekt
     * @param \Psr\Http\Message\ResponseInterface      $response das PSR-7
     *                                                           Response-Objekt
     * @param callable                                 $next     das nächste Middleware-Callable
     *
     * @return \Psr\Http\Message\ResponseInterface das neue Response-Objekt
     *
     * @SuppressWarnings(PHPMD.Superglobals)
     */
    public function __invoke(Request $request, Response $response, $next)
    {
        $route = $request->getAttribute('route');
        $course_id = $route->getArgument('course_id');

        if (!$course_id || !$GLOBALS['perm']->have_studip_perm($this->perms, $course_id)) {
            throw new Error('Access Denied', 403);
        }

        return $next($request, $response);

    }
}
