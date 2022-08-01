<?php
class LcInfotextCourse extends Migration
{
    public function description()
    {
        return 'Add course config for infotext';
    }

    public function up()
    {
        $db = DBManager::get();

        // migrate setting from seminare.public_topics
        $stmt = $db->prepare('INSERT INTO config (field, value, type, `range`, mkdate, chdate, description)
                              VALUES (:name, :value, :type, :range, UNIX_TIMESTAMP(), UNIX_TIMESTAMP(), :description)');
        $stmt->execute([
            'name'        => 'LUCKY_CONSULTATION_INFOTEXT',
            'description' => 'Dieser Infotext wird am Anfang der Seite angezeigt im Losbasierten Sprechstundenplugin.',
            'range'       => 'course',
            'type'        => 'text',
            'value'       => ''
        ]);
    }

    public function down()
    {
        $db = DBManager::get();

        $db->exec("DELETE config, config_values FROM config LEFT JOIN config_values USING(field)
                   WHERE field = 'LUCKY_CONSULTATION_INFOTEXT'");
    }
}
