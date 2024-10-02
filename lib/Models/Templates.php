<?php

namespace LuckyConsultation\Models;

class Templates extends UPMap
{
    public static function configure($config = [])
    {
        $config['db_table'] = 'luckyconsultation_templates';

        parent::configure($config);
    }
}