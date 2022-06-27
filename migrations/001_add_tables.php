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

        $db->exec("CREATE TABLE `luckyconsultation_dates` (
            `id` int NOT NULL AUTO_INCREMENT PRIMARY KEY,
            `course_id` varchar(32) NOT NULL,
            `description` varchar(255) NOT NULL,
            `start` datetime NOT NULL,
            `end` datetime NOT NULL,
            `pool` int NULL,
            `mkdate` datetime NOT NULL ON UPDATE CURRENT_TIMESTAMP,
            `chdate` datetime NOT NULL,
            FOREIGN KEY (`pool`) REFERENCES `luckyconsultation_pools` (`id`)  ON DELETE SET NULL ON UPDATE CASCADE
        )");

        SimpleORMap::expireTableScheme();
    }

    function down()
    {
        $db = DBManager::get();

        $db->exec("DROP TABLE `luckyconsultation_dates`");
        $db->exec("DROP TABLE `luckyconsultation_pools`");
    }
}