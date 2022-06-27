<?php

namespace LuckyConsultation\Routes\Dates;

use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use LuckyConsultation\Errors\AuthorizationFailedException;
use LuckyConsultation\Errors\Error;
use LuckyConsultation\LuckyConsultationTrait;
use LuckyConsultation\LuckyConsultationController;
use LuckyConsultation\Models\Dates;

class MyDatesList extends LuckyConsultationController
{
    use LuckyConsultationTrait;

    public function __invoke(Request $request, Response $response, $args)
    {
        global $user, $perm;

        if ($perm->have_studip_perm('tutor', $args['course_id'])) {
            return $this->createEmptyResponse($response);
        } else {
            $my_dates = Dates::findByUser_id($user->id);

            return $this->createResponse($this->toArray($my_dates), $response);
        }
    }
}
