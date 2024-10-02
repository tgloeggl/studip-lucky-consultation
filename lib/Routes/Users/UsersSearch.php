<?php

namespace LuckyConsultation\Routes\Users;

use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use LuckyConsultation\Errors\AuthorizationFailedException;
use LuckyConsultation\Errors\Error;
use LuckyConsultation\LuckyConsultationTrait;
use LuckyConsultation\LuckyConsultationController;

class UsersSearch extends LuckyConsultationController
{
    use LuckyConsultationTrait;

    public function __invoke(Request $request, Response $response, $args)
    {
        global $user;

        $search_term = $args['term'];

        // 08422bc3d757b8d2f63d97d306013f83 -  Klinische Psychologie und Psychotherapie
        $institute = '08422bc3d757b8d2f63d97d306013f83';

        $data = [
            ':input' => "%" . $search_term . "%",
            ':institute' => [$institute],
            ':permission' => ['dozent']
        ];

        $sql =  "SELECT DISTINCT auth_user_md5.user_id
                FROM auth_user_md5
                LEFT JOIN user_inst ON user_inst.user_id = auth_user_md5.user_id
                LEFT JOIN user_info ON auth_user_md5.user_id = user_info.user_id
                WHERE (
                    CONCAT(auth_user_md5.Nachname, ' ', auth_user_md5.Vorname, ' ', auth_user_md5.Nachname) LIKE REPLACE(:input, ' ', '% ')
                    OR CONCAT(auth_user_md5.Nachname, ', ', auth_user_md5.Vorname) LIKE :input
                    OR auth_user_md5.username LIKE :input
                    )
                    AND user_inst.Institut_id IN (:institute)
                    AND user_inst.inst_perms IN (:permission)
                    AND (
                        auth_user_md5.visible NOT IN ('never', 'no', 'unknown')
                        OR auth_user_md5.perms = 'dozent'
                    )
                ORDER BY auth_user_md5.Nachname, auth_user_md5.Vorname, auth_user_md5.username";

        $db = \DBManager::get();
        $stmt = $db->prepare($sql);
        $stmt->execute($data);

        $users = [];

        while ($user_id = $stmt->fetchColumn()) {
            $users[] = [
                'value' => $user_id,
                'name'  => \get_fullname($user_id)
            ];
        }
        return $this->createResponse([
            'users' => $users
        ], $response);
    }
}
