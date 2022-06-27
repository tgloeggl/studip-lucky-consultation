<?php

namespace LuckyConsultation\Routes\Dates;

use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use LuckyConsultation\Errors\AuthorizationFailedException;
use LuckyConsultation\Errors\Error;
use LuckyConsultation\LuckyConsultationTrait;
use LuckyConsultation\LuckyConsultationController;
use LuckyConsultation\Models\Dates;
use LuckyConsultation\Models\WaitingList as WaitingListOrm;

class WaitingList extends LuckyConsultationController
{
    use LuckyConsultationTrait;

    public function __invoke(Request $request, Response $response, $args)
    {
        global $user;

        $waiting_list = WaitingListOrm::getForUserInCourse($user->id, $args['course_id']);

        return $this->createResponse($this->toArray($waiting_list), $response);
    }
}
