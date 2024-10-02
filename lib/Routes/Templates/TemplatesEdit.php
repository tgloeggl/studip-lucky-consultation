<?php

namespace LuckyConsultation\Routes\Templates;

use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use LuckyConsultation\Errors\AuthorizationFailedException;
use LuckyConsultation\Errors\Error;
use LuckyConsultation\LuckyConsultationTrait;
use LuckyConsultation\LuckyConsultationController;
use LuckyConsultation\Models\Templates;

class TemplatesEdit extends LuckyConsultationController
{
    use LuckyConsultationTrait;

    public function __invoke(Request $request, Response $response, $args)
    {
        global $user;

        $json = $this->getRequestData($request);

        foreach ($json['templates'] as $json_template) {
            $template = Templates::find($json_template['id']);

            if (empty($template)) {
                $template = new Templates();
            }

            $template->setData($json_template);
            $template->store();

        }

        $templates = Templates::findBySql(1);
        return $this->createResponse($this->toArray($templates), $response);

    }
}
