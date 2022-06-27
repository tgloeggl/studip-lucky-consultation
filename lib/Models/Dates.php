<?php

namespace LuckyConsultation\Models;
class Dates extends UPMap 
{
    public static function configure($config = [])
    {
        $config['db_table'] = 'luckyconsultation_dates';

        parent::configure($config);
    }
}