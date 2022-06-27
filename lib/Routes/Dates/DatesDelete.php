<?php

namespace LuckyConsultation\Routes\Dates;

use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use LuckyConsultation\Errors\AuthorizationFailedException;
use LuckyConsultation\Errors\Error;
use LuckyConsultation\LuckyConsultationTrait;
use LuckyConsultation\LuckyConsultationController;
use LuckyConsultation\Models\Dates;

class DatesDelete extends LuckyConsultationController
{
    use LuckyConsultationTrait;

    public function __invoke(Request $request, Response $response, $args)
    {
        global $user;

        $date = Dates::find($args['date_id']);

        if ($date->course_id == $args['course_id']) {
            $date->delete();
        } else {
            throw new Error('Access Denied', 403);
        }

        $dates = Dates::findByCourse_id($args['course_id']);

        return $this->createResponse($this->toArray($dates), $response);
    }
}
