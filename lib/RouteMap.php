<?php

namespace LuckyConsultation;

use LuckyConsultation\Providers\StudipServices;

class RouteMap
{
    public function __construct(\Slim\App $app)
    {
        $this->app = $app;
    }

    public function __invoke()
    {
        $container = $this->app->getContainer();

        $this->app->group('', [$this, 'authenticatedRoutes'])
            ->add(new Middlewares\Authentication($container[StudipServices::AUTHENTICATOR]))
            ->add(new Middlewares\RemoveTrailingSlashes);


        $this->app->group('', [$this, 'courseRoutes'])
            ->add(new Middlewares\CheckCourse('autor'))
            ->add(new Middlewares\Authentication($container[StudipServices::AUTHENTICATOR]))
            ->add(new Middlewares\RemoveTrailingSlashes);

        $this->app->group('', [$this, 'privilegedRoutes'])
            ->add(new Middlewares\CheckCourse('tutor'))
            ->add(new Middlewares\Authentication($container[StudipServices::AUTHENTICATOR]))
            ->add(new Middlewares\RemoveTrailingSlashes);

        $this->app->get('/discovery', Routes\DiscoveryIndex::class);
    }

    public function authenticatedRoutes()
    {
        $this->app->get('/user', Routes\Users\UsersShow::class);
    }

    public function courseRoutes()
    {
        $this->app->get('/course/{course_id}/waitinglist', Routes\Dates\WaitingList::class);
        $this->app->put('/course/{course_id}/waitinglist/{date_id}', Routes\Dates\WaitingListAdd::class);
        $this->app->delete('/course/{course_id}/waitinglist/{date_id}', Routes\Dates\WaitingListDelete::class);

        $this->app->get('/course/{course_id}/pools', Routes\Pools\PoolsList::class);
        $this->app->get('/course/{course_id}/dates', Routes\Dates\DatesList::class);
        $this->app->get('/course/{course_id}/mydates', Routes\Dates\MyDatesList::class);
    }

    public function privilegedRoutes()
    {
        $this->app->post('/course/{course_id}/pools', Routes\Pools\PoolsAdd::class);
        $this->app->put('/course/{course_id}/pools', Routes\Pools\PoolsEdit::class);
        $this->app->delete('/course/{course_id}/pools/{pool_id}', Routes\Pools\PoolsDelete::class);


        $this->app->post('/course/{course_id}/dates', Routes\Dates\DatesAdd::class);
        $this->app->put('/course/{course_id}/dates', Routes\Dates\DatesEdit::class);
        $this->app->delete('/course/{course_id}/dates/{date_id}', Routes\Dates\DatesDelete::class);
    }
}
