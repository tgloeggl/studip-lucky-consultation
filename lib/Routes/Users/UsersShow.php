<?php

namespace LuckyConsultation\Routes\Users;

use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use LuckyConsultation\Errors\AuthorizationFailedException;
use LuckyConsultation\Errors\Error;
use LuckyConsultation\LuckyConsultationTrait;
use LuckyConsultation\LuckyConsultationController;

class UsersShow extends LuckyConsultationController
{
    use LuckyConsultationTrait;

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
                $this->container->get('roles')['admin']
            )
        ];

        return $this->createResponse([
            'type' => 'user',
            'id'   => $user->id,
            'data' => $data
        ], $response);
    }
}
