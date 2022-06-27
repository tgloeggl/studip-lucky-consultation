<?php

namespace LuckyConsultation\Models;

class WaitingList extends UPMap 
{
    public static function configure($config = [])
    {
        $config['db_table'] = 'luckyconsultation_waitinglist';

        parent::configure($config);
    }
}