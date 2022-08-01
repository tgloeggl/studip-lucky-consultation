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

                $num = random_int(0, sizeof($list) - 1);
                $winner = $list[$num];

                echo "Date: ({$date->id}) {$date->start}, Winner is: {$winner['user_id']}, Lot number: $num\n";

                $date->user_id = $winner['user_id'];
                $date->store();

                $date->waitinglist = [];
                WaitingList::deleteByDates_id($date->id);
            }

            $pool->lots_drawn = 1;
            $pool->store();
        }
    }

}
