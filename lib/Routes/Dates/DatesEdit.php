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

class DatesEdit extends LuckyConsultationController
{
    use LuckyConsultationTrait;

    public function __invoke(Request $request, Response $response, $args)
    {
        global $user;

        $json = $this->getRequestData($request);

        foreach ($json['dates'] as $json_date) {
            $date = !empty($json_date['id']) ? Dates::find($json_date['id']) : null;

            if (empty($date)) {
                $date = new Dates();
                $date->course_id = $args['course_id'];
            }

            if (!empty($date) && $date->course_id == $args['course_id']) {
                unset($json_date['attributes']['id']);
                unset($json_date['attributes']['course_id']);

                $clear_waitinglist = !empty($date->id)
                    && $this->startChanged($date->start, $json_date['attributes']['start']);

                $date->setData($json_date['attributes']);
                $date->store();

                if ($clear_waitinglist) {
                    WaitingList::deleteByDates_id($date->id);
                }
            } else {
                throw new Error('Access Denied', 403);
            }
        }

        // delete dates (id any)
        foreach ($json['delete'] as $date_id) {
            $date = Dates::find($date_id);

            if (!empty($date) && $date->course_id == $args['course_id']) {
                $date->delete();
            }
        }

        $dates = Dates::findBySQL('course_id = ? ORDER BY start DESC', [$args['course_id']]);

        return $this->createResponse($this->toArray($dates), $response);
    }

    private function startChanged($old_start, $new_start)
    {
        if (empty($old_start) || empty($new_start)) {
            return $old_start != $new_start;
        }

        return strtotime($old_start) !== strtotime($new_start);
    }
}
