<?php

namespace LuckyConsultationsultationsultation\Routes\Users;

use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use LuckyConsultationsultationsultation\Errors\AuthorizationFailedException;
use LuckyConsultationsultationsultation\Errors\Error;
use LuckyConsultationsLuckyConsultationsLuckyConsultation\LuckyConsultationTrait;
use LuckyConsultationsLuckyConsultationsLuckyConsultation\LuckyConsultationController;

class UsersShow extends LuckyConsultationsultationsultationController
{
    use LuckyConsultationsultationsultationTrait;

    public function __invoke(Request $request, Response $response, $args)
    {
        global $user;

        $data = [
            'id'       => $user->id,
            'username' => $user->username,
            'fullname' => get_fullname($user->id),
            'status'   => $user->perms,
            'admin'    => \RolePersistence::isAssignedRole(
                $GLOBALS['user']->user_id,
                $this->container['roles']['admin'])
        ];

        return $this->createResponse([
            'type' => 'user',
            'id'   => $user->id,
            'data' => $data
        ], $response);
    }
}
