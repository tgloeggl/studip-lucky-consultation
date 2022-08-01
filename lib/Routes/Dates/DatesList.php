<?php

namespace LuckyConsultation\Routes\Dates;

use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use LuckyConsultation\Errors\AuthorizationFailedException;
use LuckyConsultation\Errors\Error;
use LuckyConsultation\LuckyConsultationTrait;
use LuckyConsultation\LuckyConsultationController;
use LuckyConsultation\Models\Dates;

class DatesList extends LuckyConsultationController
{
    use LuckyConsultationTrait;

    public function __invoke(Request $request, Response $response, $args)
    {
        global $user, $perm;

        if ($perm->have_studip_perm('tutor', $args['course_id'])) {
            $dates = Dates::findByCourse_id($args['course_id']);
        } else {
            // check, if user has already a date in one or many of the pools
            $my_dates = new \SimpleCollection(Dates::findByUser_id($user->id));
            $pool_ids = $my_dates->pluck('pool');

            if (!empty($pool_ids)) {
                $dates = Dates::findBySql('JOIN luckyconsultation_pools AS lp
                    ON (lp.id = pool)
                    WHERE luckyconsultation_dates.course_id = :course_id
                        AND lp.date > NOW()
                        AND luckyconsultation_dates.start > NOW()
                        AND pool NOT IN (' . implode(', ', $pool_ids) . ')',
                    [
                    'course_id' => $args['course_id']
                    ]
                );
            } else {
                $dates = Dates::findBySql('JOIN luckyconsultation_pools AS lp
                    ON (lp.id = pool)
                    WHERE luckyconsultation_dates.course_id = :course_id
                        AND lp.date > NOW()
                        AND luckyconsultation_dates.start > NOW()',
                    [
                    'course_id' => $args['course_id']
                    ]
                );
            }
        }

        return $this->createResponse($this->toArray($dates), $response);
    }
}
