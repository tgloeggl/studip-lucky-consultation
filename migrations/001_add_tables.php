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
            `course_id` varchar(32) NOT NULL COLLATE latin1_bin,
            `name` varchar(255) NOT NULL,
            `date` datetime NOT NULL,
            `lots_drawn` tinyint NOT NULL DEFAULT '0',
            `mkdate` datetime NOT NULL ON UPDATE CURRENT_TIMESTAMP,
            `chdate` datetime NOT NULL
        )");

        $db->exec("CREATE TABLE `luckyconsultation_dates` (
            `id` int NOT NULL AUTO_INCREMENT PRIMARY KEY,
            `course_id` varchar(32) NOT NULL COLLATE latin1_bin,
            `user_id` varchar(32) NULL COLLATE latin1_bin,
            `description` varchar(255) NOT NULL,
            `start` datetime NOT NULL,
            `pool` int NULL,
            `mkdate` datetime NOT NULL ON UPDATE CURRENT_TIMESTAMP,
            `chdate` datetime NOT NULL,
            FOREIGN KEY (`pool`) REFERENCES `luckyconsultation_pools` (`id`)  ON DELETE SET NULL ON UPDATE CASCADE
        )");

        $db->exec("CREATE TABLE `luckyconsultation_waitinglist` (
            `dates_id` int NOT NULL,
            `user_id` varchar(32) NOT NULL COLLATE latin1_bin,
            `mkdate` datetime NOT NULL ON UPDATE CURRENT_TIMESTAMP,
            `chdate` datetime NOT NULL,
            PRIMARY KEY (`dates_id`, `user_id`),
            FOREIGN KEY (`dates_id`) REFERENCES `luckyconsultation_dates` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
        )");

        SimpleORMap::expireTableScheme();
    }

    function down()
    {
        $db = DBManager::get();

        $db->exec("SET foreign_key_checks = 0");
        $db->exec("DROP TABLE `luckyconsultation_dates`");
        $db->exec("DROP TABLE `luckyconsultation_pools`");
        $db->exec("DROP TABLE `luckyconsultation_waitinglist`");
        $db->exec("SET foreign_key_checks = 1");
    }
}