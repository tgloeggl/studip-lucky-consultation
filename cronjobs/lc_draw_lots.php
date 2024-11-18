<?php
require_once __DIR__ . '/../vendor/autoload.php';
require_once 'lib/messaging.inc.php';

use LuckyConsultation\Models\Dates;
use LuckyConsultation\Models\Pools;
use LuckyConsultation\Models\WaitingList;
use LuckyConsultation\Models\Templates;

class DrawLots extends CronJob
{

    public static function getName()
    {
        return _('Losbasierte Sprechstunden - Losen der Zuweisungen');
    }

    public static function getDescription()
    {
        return _('Losbasierte Sprechstunden: Lost fällige Losungen aus.');
    }

    public function execute($last_result, $parameters = array())
    {
        // set to default system language
        setTempLanguage();

        // get all pools to dra lots for
        $pools = Pools::findBySQL('lots_drawn = 0 AND date <= NOW()');

        $templates = [];
        foreach(Templates::findBySQL(1) as $t) {
            $templates[$t->id] = $t->template;
        }

        foreach ($pools as $pool) {
            // get dates and draw lots
            foreach ($pool->dates as $date) {
                $list = $date->waitinglist->toArray();

                if (!empty($list)) {
                    $num = random_int(0, sizeof($list) - 1);
                    $winner = $list[$num];

                    echo "Date: ({$date->id}) {$date->start}, Winner is: {$winner['user_id']}, Lot number: $num\n";

                    $date->user_id = $winner['user_id'];
                    $date->store();

                    // delete user from all other dates of the same pool
                    $entries = new \SimpleCollection(WaitingList::findBySQL('JOIN luckyconsultation_dates AS ld
                        ON (dates_id = ld.id)
                        WHERE
                            luckyconsultation_waitinglist.user_id = :user_id
                            AND ld.pool = :pool_id',
                        [
                            ':user_id' => $winner['user_id'],
                            ':pool_id' => $date->pool
                        ]
                    ));

                    $date_ids = $entries->pluck('dates_id');

                    WaitingList::deleteBySQL('dates_id IN ('
                        . implode(', ', $date_ids)
                        . ') AND user_id = :user_id',
                        [
                            ':user_id' => $winner['user_id']
                        ]
                    );

                    // clear waitinglist for this date
                    $date->waitinglist = [];
                    WaitingList::deleteByDates_id($date->id);

                    // send success mail to winner
                    $messaging = new \Messaging();

                    $template = $templates[$pool->template];

                    $replacements = [
                        '##fullname##'      => get_fullname($winner['user_id']),
                        '##therapist##'     => $date->description,
                        '##date##'          => date('d.m.Y', strtotime($date->start)),
                        '##time##'          => date('H:i', strtotime($date->start)),
                        '##weekday##'       => strftime('%A', strtotime($date->start)),
                        '##fs_start##'      => $date->fs_start,
                        '##fs_slot##'       => $date->fs_slot,
                        '##fs_room##'       => $date->fs_room,
                        '##ko_date##'       => $date->ko_date,
                        '##ko_room##'       => $date->ko_room,
                    ];

                    foreach ($replacements as $key => $text) {
                        $template = str_replace($key, $text, $template);
                    }

                    $messaging->insert_message($template,
                        get_username($winner['user_id']), '____%system%____',
                        false, false, false, false,
                        sprintf(
                            _('Ihnen wurde ein Termin am %s bei %s zugelost!'),
                            $date->start, $date->description
                        )
                    );

                    $messaging->insert_message($template,
                        get_username($date['therapist_id']), '____%system%____',
                        false, false, false, false,
                        sprintf(
                            _('%s wurde ein Termin am %s bei Ihnen zugelost!'),
                            get_fullname($winner['user_id']), $date->start
                        )
                    );
                }
            }

            $pool->lots_drawn = 1;
            $pool->store();
        }
    }

}
