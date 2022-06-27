<?php

namespace LuckyConsultation\Models;

class Pools extends UPMap 
{
    public static function configure($config = [])
    {
        $config['db_table'] = 'luckyconsultation_pools';

        parent::configure($config);
    }
}