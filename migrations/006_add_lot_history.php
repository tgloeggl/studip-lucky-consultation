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

        $db->exec("ALTER TABLE `luckyconsultation_dates`
            ADD `history` text NULL AFTER pool
        ");

        SimpleORMap::expireTableScheme();
    }

    function down()
    {

    }
}