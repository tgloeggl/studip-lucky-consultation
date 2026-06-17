<?php

class AddApprovedToDates extends Migration
{
    function description()
    {
        return 'add approval state for consultation dates';
    }

    function up()
    {
        $db = DBManager::get();

        $check = $db->query("SHOW COLUMNS FROM `luckyconsultation_dates` LIKE 'approved'");
        if ($check->rowCount() === 0) {
            $db->exec("ALTER TABLE `luckyconsultation_dates`
                ADD COLUMN `approved` tinyint NOT NULL DEFAULT '0'
                AFTER `pool`");
        }
    }

    function down()
    {
        $db = DBManager::get();

        $check = $db->query("SHOW COLUMNS FROM `luckyconsultation_dates` LIKE 'approved'");
        if ($check->rowCount() > 0) {
            $db->exec("ALTER TABLE `luckyconsultation_dates`
                DROP COLUMN `approved`");
        }
    }
}
