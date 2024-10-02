<?php

class AddMailTemplates extends Migration
{
    private const PP = 'Liebe*r ##fullname##,

gerne möchte ich Ihnen mitteilen, dass Sie folgenden Termin für die Berufsqualifizierende Tätigkeit III ambulant bei ##therapist## für den PP-Bereich erhalten haben:

Ab dem ##date## , wöchentlich ##weekday##, Therapiestart ist um ##time## Uhr.

Das dazugehörige Fallseminar beginnt am ##fs_start## und findet in Raum ##fs_room## (Institut für Psychologie, Lise-Meitner-Straße 3, Gebäude 75) statt. Bitte stellen Sie sich darauf ein, dass das begleitende Fallseminar bis Mitte November läuft. Die weiteren Termine werden im ersten Fallseminartermin bekannt gegeben.

Die einmalige Kick-Off-Sitzung erfolgt am ##ko_date## Uhr.
Kommen Sie hierfür bitte in Raum ##ko_room##.

In der Kick-Off-Sitzung erhalten Sie die Informationen zu Ihrem/r Patienten/in, welche im Erstgespräch bereits erfasst wurden (u.a. Verdachtsdiagnosen, mögliche Ziele/Themen der Therapie). Sie werden dort auch über den Ablauf der Therapie und organisatorische Belange informiert.

Voraussetzung für den Beginn der Therapie ist das Vorliegen der von Ihnen zu unterzeichnenden Vereinbarungen (Schweigepflicht und Verhaltenskodex). Sollten Sie diese Dokumente noch nicht unterschrieben haben, holen Sie Letzteres bitte umgehend nach.

Ich wünsche Ihnen viel Erfolg und alles Gute.

Mit freundlichen Grüßen';

    private const KJP = 'Liebe*r ##fullname##,

gerne möchte ich Ihnen mitteilen, dass Sie folgenden Termin für die Berufsqualifizierende Tätigkeit III ambulant bei ##therapist## für den KJP-Bereich erhalten haben:

Ab dem ##date## , wöchentlich ##weekday##, Therapiestart ist um ##time## Uhr.

Die ersten beiden, verpflichtenden Termine des dazugehörigen Fallseminares finden am ##fs_start## von ##fs_slot## Uhr in Raum ##fs_room## (Institut für Psychologie, Lise-Meitner-Straße 3, Gebäude 75) statt. Bitte stellen Sie sich darauf ein, dass das begleitende Fallseminar bis Mitte November läuft. Die weiteren fünf Termine werden im ersten Fallseminartermin bekannt gegeben.

Die einmalige Kick-Off-Sitzung erfolgt am ##ko_date## Uhr.
Kommen Sie hierfür bitte in Raum ##ko_room##.

In der Kick-Off-Sitzung erhalten Sie die Informationen zu Ihrem/r Patienten/in, welche im Erstgespräch bereits erfasst wurden (u.a. Verdachtsdiagnosen, mögliche Ziele/Themen der Therapie). Sie werden dort auch über den Ablauf der Therapie und organisatorische Belange informiert.

Voraussetzung für den Beginn der Therapie ist das Vorliegen der von Ihnen zu unterzeichnenden Vereinbarungen (Schweigepflicht und Verhaltenskodex). Sollten Sie diese Dokumente noch nicht unterschrieben eingereicht haben, holen Sie dieses bitte umgehend nach.

Ich wünsche Ihnen viel Erfolg und alles Gute.

Mit freundlichen Grüßen';

    function description()
    {
        return 'Changes tables for mail templates';
    }

    function up()
    {
        $db = DBManager::get();

        $db->exec("ALTER TABLE `luckyconsultation_pools`
            ADD `template` ENUM('PP', 'KJP') NOT NULL DEFAULT 'PP' AFTER lots_drawn");

        $db->exec("ALTER TABLE `luckyconsultation_dates`
            ADD fs_start varchar(255) NULL,
            ADD fs_slot  varchar(255) NULL,
            ADD fs_room  varchar(255) NULL,
            ADD ko_date  varchar(255) NULL,
            ADD ko_room  varchar(255) NULL
        ");

        $db->exec("CREATE TABLE `luckyconsultation_templates` (
            `id` ENUM('PP', 'KJP') NOT NULL PRIMARY KEY,
            `template` TEXT NULL,
            `mkdate` datetime NOT NULL ON UPDATE CURRENT_TIMESTAMP,
            `chdate` datetime NOT NULL
        )");

        $stmt = $db->prepare("REPLACE INTO luckyconsultation_templates
            (id, template) VALUES
            ('PP', :pp),
            ('KJP', :kjp)
        ");

        $stmt->execute($data = [
            ':kjp' => self::KJP,
            ':pp'  => self::PP
        ]);

        SimpleORMap::expireTableScheme();
    }

    function down()
    {

    }
}