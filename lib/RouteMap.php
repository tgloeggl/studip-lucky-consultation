<?php

namespace LuckyConsultation;

use Psr\Container\ContainerInterface;
use Slim\Routing\RouteCollectorProxy;

class RouteMap
{
    public function __construct(ContainerInterface $container)
    {
        $this->container = $container;
    }

    public function __invoke(RouteCollectorProxy $group)
    {
        $group->group('', [$this, 'authenticatedRoutes'])
            ->add(new Middlewares\Authentication($this->container->get("studip-authenticator")))
            ->add(new Middlewares\RemoveTrailingSlashes);


        $group->group('', [$this, 'courseRoutes'])
            ->add(new Middlewares\CheckCourse('autor'))
            ->add(new Middlewares\Authentication($this->container->get("studip-authenticator")))
            ->add(new Middlewares\RemoveTrailingSlashes);

        $group->group('', [$this, 'privilegedRoutes'])
            ->add(new Middlewares\CheckCourse('tutor'))
            ->add(new Middlewares\Authentication($this->container->get("studip-authenticator")))
            ->add(new Middlewares\RemoveTrailingSlashes);

        $group->get('/discovery', Routes\DiscoveryIndex::class);
    }

    public function authenticatedRoutes(RouteCollectorProxy $group)
    {
        $group->get('/user', Routes\Users\UsersShow::class);
    }

    public function courseRoutes(RouteCollectorProxy $group)
    {
        $group->get('/course/{course_id}/waitinglist', Routes\Dates\WaitingList::class);
        $group->put('/course/{course_id}/waitinglist/{date_id}', Routes\Dates\WaitingListAdd::class);
        $group->delete('/course/{course_id}/waitinglist/{date_id}', Routes\Dates\WaitingListDelete::class);

        $group->get('/course/{course_id}/pools', Routes\Pools\PoolsList::class);
        $group->get('/course/{course_id}/dates', Routes\Dates\DatesList::class);
        $group->get('/course/{course_id}/mydates', Routes\Dates\MyDatesList::class);

        $group->get('/course/{course_id}/infotext', Routes\Infotext\InfotextShow::class);
    }

    public function privilegedRoutes(RouteCollectorProxy $group)
    {
        $group->post('/course/{course_id}/pools', Routes\Pools\PoolsAdd::class);
        $group->put('/course/{course_id}/pools', Routes\Pools\PoolsEdit::class);
        $group->delete('/course/{course_id}/pools/{pool_id}', Routes\Pools\PoolsDelete::class);


        $group->post('/course/{course_id}/dates', Routes\Dates\DatesAdd::class);
        $group->put('/course/{course_id}/dates', Routes\Dates\DatesEdit::class);
        $group->delete('/course/{course_id}/dates/{date_id}', Routes\Dates\DatesDelete::class);
        $group->delete('/course/{course_id}/dates/{date_id}/user', Routes\Dates\DatesDeleteUser::class);

        $group->put('/course/{course_id}/infotext', Routes\Infotext\InfotextEdit::class);
    }
}
