<?php

namespace LuckyConsultation\Routes\Dates;

use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use LuckyConsultation\Errors\AuthorizationFailedException;
use LuckyConsultation\Errors\Error;
use LuckyConsultation\LuckyConsultationTrait;
use LuckyConsultation\LuckyConsultationController;
use LuckyConsultation\Models\Dates;
use LuckyConsultation\Models\WaitingList;

class WaitingListAdd extends LuckyConsultationController
{
    use LuckyConsultationTrait;

    public function __invoke(Request $request, Response $response, $args)
    {
        global $user;

        // check, if date belongs to course and user can add to it
        $date = Dates::findOneBySql('course_id = :course_id AND id = :date_id', [
            ':course_id' => $args['course_id'],
            ':date_id'   => $args['date_id']
        ]);

        if ($date) {
            $wl = new WaitingList;
            $wl->setData([
                'dates_id' => $args['date_id'],
                'user_id'  => $user->id
            ]);
            $wl->store();
        }

        $waiting_list = WaitingList::getForUserInCourse($user->id, $args['course_id']);

        return $this->createResponse($this->toArray($waiting_list), $response);
    }
}
