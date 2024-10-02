<?php

namespace LuckyConsultation\Models;

class WaitingList extends UPMap
{
    public static function configure($config = [])
    {
        $config['db_table'] = 'luckyconsultation_waitinglist';

        parent::configure($config);
    }

    public static function getForUserInCourse($user_id, $course_id)
    {
        return self::findBySql('JOIN luckyconsultation_dates AS ld
            ON(ld.course_id = :course_id AND ld.id = dates_id)
            WHERE luckyconsultation_waitinglist.user_id = :user_id',
        [
            'course_id' => $course_id,
            'user_id'   => $user_id
        ]);
    }

    public function toArray($only_these_fields = null)
    {
        $data = parent::toArray($only_these_fields);

        $data['fullname'] = \get_fullname($this->user_id);

        return $data;
    }
}