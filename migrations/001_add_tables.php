<?php

class AddTables extends Migration
{
    function description() 
    {
        return 'Add tables for LuckyConsultation plugin';
    }

    function up()
    {
        $db = DBManager::get();

        $db->exec("CREATE TABLE `luckyconsultation_pools` (
            `id` int NOT NULL AUTO_INCREMENT PRIMARY KEY,
            `course_id` varchar(32) NOT NULL,
            `name` varchar(255) NOT NULL,
            `date` datetime NOT NULL,
            `mkdate` datetime NOT NULL ON UPDATE CURRENT_TIMESTAMP,
            `chdate` datetime NOT NULL
        )");
    }
}