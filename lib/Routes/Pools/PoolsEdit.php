<?php

namespace LuckyConsultation\Routes\Pools;

use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use LuckyConsultation\Errors\AuthorizationFailedException;
use LuckyConsultation\Errors\Error;
use LuckyConsultation\LuckyConsultationTrait;
use LuckyConsultation\LuckyConsultationController;
use LuckyConsultation\Models\Pools;

class PoolsEdit extends LuckyConsultationController
{
    use LuckyConsultationTrait;

    public function __invoke(Request $request, Response $response, $args)
    {
        global $user;

        $json = $this->getRequestData($request);

        $pool = Pools::find($json['id']);
        
        if ($pool->course_id == $args['course_id']) {
            $pool->setData([
                'name' => $json['name'],
                'date' => $json['date']
            ]);
            $pool->store();
        } else {
            throw new Error('Access Denied', 403);
        }

        $pools = Pools::findByCourse_id($args['course_id']);

        return $this->createResponse($this->toArray($pools), $response);
    }
}
