<?php

class AddTherapistId extends Migration
{
    function description()
    {
        return 'add therapist id additional to description for dates';
    }

    function up()
    {
        $db = DBManager::get();

        $db->exec("ALTER TABLE `luckyconsultation_dates`
            ADD `therapist_id` varchar(32) NULL COLLATE latin1_bin
        ");

        SimpleORMap::expireTableScheme();
    }

    function down()
    {

    }
}