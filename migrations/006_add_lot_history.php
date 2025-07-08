<?php

class AddLotHistory extends Migration
{
    function description()
    {
        return 'add history for lot draws on dates';
    }

    function up()
    {
        $db = DBManager::get();

        // Check if the 'history' column already exists
        $check = $db->query("SHOW COLUMNS FROM `luckyconsultation_dates` LIKE 'history'");
        $exists = $check->fetchColumn();

        if (!$exists) {
            $db->exec("ALTER TABLE `luckyconsultation_dates`
                ADD `history` text NULL AFTER pool
            ");

            SimpleORMap::expireTableScheme();
        }
    }

    function down()
    {

    }
}