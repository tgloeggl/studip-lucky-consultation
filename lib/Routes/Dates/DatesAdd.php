<?php

namespace LuckyConsultation\Routes\Dates;

use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use LuckyConsultation\Errors\AuthorizationFailedException;
use LuckyConsultation\Errors\Error;
use LuckyConsultation\LuckyConsultationTrait;
use LuckyConsultation\LuckyConsultationController;
use LuckyConsultation\Models\Dates;

class DatesAdd extends LuckyConsultationController
{
    use LuckyConsultationTrait;

    public function __invoke(Request $request, Response $response, $args)
    {
        global $user;

        $json = $this->getRequestData($request);

        $date = new Dates;
        $date->setData($json);
        $date->course_id = $args['course_id'];
        $date->store();

        $dates = Dates::findBySQL('course_id = ? ORDER BY start DESC', [$args['course_id']]);

        return $this->createResponse($this->toArray($dates), $response);
    }
}
