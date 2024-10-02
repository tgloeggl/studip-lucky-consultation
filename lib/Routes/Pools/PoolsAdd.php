<?php

namespace LuckyConsultation\Routes\Pools;

use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use LuckyConsultation\Errors\AuthorizationFailedException;
use LuckyConsultation\Errors\Error;
use LuckyConsultation\LuckyConsultationTrait;
use LuckyConsultation\LuckyConsultationController;
use LuckyConsultation\Models\Pools;

class PoolsAdd extends LuckyConsultationController
{
    use LuckyConsultationTrait;

    public function __invoke(Request $request, Response $response, $args)
    {
        $json = $this->getRequestData($request);

        $pool = new Pools;
        $pool->setData($json);
        $pool->course_id = $args['course_id'];
        $pool->store();

        $pools = Pools::findByCourse_id($args['course_id']);

        return $this->createResponse($this->toArray($pools), $response);
    }
}
