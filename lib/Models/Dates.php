<?php

namespace LuckyConsultation\Models;
class Dates extends UPMap
{
    public static function configure($config = [])
    {
        $config['db_table'] = 'luckyconsultation_dates';

        $config['belongs_to']['user'] = [
            'class_name' => 'User',
            'foreign_key' => 'user_id'
        ];

        $config['has_many']['waitinglist'] = [
            'class_name' => 'LuckyConsultation\\Models\\WaitingList',
            'foreign_key' => 'id',
            'assoc_foreign_key' => 'dates_id'
        ];

        //$config['additional_fields']['username']['get'] = 'getUsername';

        $config['additional_fields']['username']['get'] = 'getUsername';
        $config['additional_fields']['fullname']['get'] = 'getFullname';
        $config['additional_fields']['waiting']['get']  = 'countWaitingList';

        parent::configure($config);
    }

    public function countWaitinglist()
    {
        return sizeof($this->waitinglist);
    }

    public function getFullname()
    {
        if ($this->user) {
            return $this->user->getFullName();
        }
    }

    public function getUsername()
    {
        if ($this->user) {
            return $this->user->username;
        }
    }
}