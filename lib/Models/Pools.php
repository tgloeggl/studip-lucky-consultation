<?php

namespace LuckyConsultation\Models;

class Pools extends UPMap
{
    public static function configure($config = [])
    {
        $config['db_table'] = 'luckyconsultation_pools';

        $config['has_many']['dates'] = [
            'class_name' => 'LuckyConsultation\\Models\\Dates',
            'foreign_key' => 'id',
            'assoc_foreign_key' => 'pool'
        ];

        parent::configure($config);
    }
}