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
        $course_id   = $args['course_id'];

        // get all institutes for the faculty of the current course
        $course = \Course::find($course_id);

        // Fallback - 9ae3b681c2e2b51c20fd3b31756a5dd4	- Institut für Psychologie
        $institute_id = '9ae3b681c2e2b51c20fd3b31756a5dd4';

        if ($course) {
            $institute_id = $course->home_institut->id;
        }

        $institute = \DBManager::get()->query("SELECT Institut_id FROM Institute
            WHERE fakultaets_id = '$institute_id'
                OR Institut_id = '$institute_id'"
        )->fetchAll(\PDO::FETCH_COLUMN);

        $data = [
            ':input' => "%" . $search_term . "%",
            ':institute' => $institute,
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
