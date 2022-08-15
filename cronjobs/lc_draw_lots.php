<?php
require_once __DIR__ . '/../vendor/autoload.php';

use LuckyConsultation\Models\Dates;
use LuckyConsultation\Models\Pools;
use LuckyConsultation\Models\WaitingList;

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
        // get all pools to dra lots for
        $pools = Pools::findBySQL('lots_drawn = 0 AND date <= NOW()');

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
                }
            }

            $pool->lots_drawn = 1;
            $pool->store();
        }
    }

}
