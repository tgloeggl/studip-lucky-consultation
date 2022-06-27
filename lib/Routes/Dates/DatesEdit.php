<?php

namespace LuckyConsultation\Routes\Dates;

use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use LuckyConsultation\Errors\AuthorizationFailedException;
use LuckyConsultation\Errors\Error;
use LuckyConsultation\LuckyConsultationTrait;
use LuckyConsultation\LuckyConsultationController;
use LuckyConsultation\Models\Dates;

class DatesEdit extends LuckyConsultationController
{
    use LuckyConsultationTrait;

    public function __invoke(Request $request, Response $response, $args)
    {
        global $user;

        $json = $this->getRequestData($request);

        $date = Dates::find($json['id']);
        
        if ($date->course_id == $args['course_id']) {
            $date->setData([
                'description' => $json['description'],
                'start'       => $json['start'],
                'end'         => $json['end'],
                'pool'        => $json['pool']
            ]);
            $date->store();
        } else {
            throw new Error('Access Denied', 403);
        }

        $dates = Dates::findByCourse_id($args['course_id']);

        return $this->createResponse($this->toArray($dates), $response);
    }
}
