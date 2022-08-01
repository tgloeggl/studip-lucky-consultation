<?php

namespace LuckyConsultation\Routes\Infotext;

use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use LuckyConsultation\Errors\AuthorizationFailedException;
use LuckyConsultation\Errors\Error;
use LuckyConsultation\LuckyConsultationTrait;
use LuckyConsultation\LuckyConsultationController;

class InfotextShow extends LuckyConsultationController
{
    use LuckyConsultationTrait;

    public function __invoke(Request $request, Response $response, $args)
    {
        return $this->createResponse([
            'infotext' => \CourseConfig::get($course->id)->LUCKY_CONSULTATION_INFOTEXT
        ], $response);
    }
}
