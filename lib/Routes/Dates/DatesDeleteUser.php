<?php

namespace LuckyConsultation\Routes\Dates;

use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use LuckyConsultation\Errors\AuthorizationFailedException;
use LuckyConsultation\Errors\Error;
use LuckyConsultation\LuckyConsultationTrait;
use LuckyConsultation\LuckyConsultationController;
use LuckyConsultation\Models\Dates;

class DatesDeleteUser extends LuckyConsultationController
{
    use LuckyConsultationTrait;

    public function __invoke(Request $request, Response $response, $args)
    {
        global $user;

        $date = Dates::find($args['date_id']);

        if (!empty($date) && $date->course_id == $args['course_id']) {
            $date->user_id = null;
            $date->store();
        } else {
            throw new Error('Access Denied', 403);
        }

        $dates = Dates::findByCourse_id($args['course_id']);

        return $this->createResponse($this->toArray($dates), $response);
    }
}
