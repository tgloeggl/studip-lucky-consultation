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

    /**
     * Executes the draw lots process for lucky consultations.
     *
     * This method performs the following tasks:
     * 1. Sets the default system language.
     * 2. Retrieves all pools that need to have lots drawn.
     * 3. Loads all templates.
     * 4. Fixates all waiting lists for later reference.
     * 5. Sorts pools by date (earliest first).
     * 6. For each pool, sorts dates by number of waitlist entries (fewest first).
     * 7. For each date, selects a winner from available users.
     * 8. Updates the date with the winner, removes the winner from other waiting lists.
     * 9. Sends notification messages to the winner and the therapist.
     * 10. Marks the pool as having had its lots drawn.
     *
     * @param mixed $last_result The result of the last execution of this job (not used in this implementation)
     * @param array $parameters Additional parameters for the job execution (not used in this implementation)
     *
     * @return void This method does not return a value
     */
    public function execute($last_result, $parameters = array())
    {
        $db = DBManager::get();

         $clear_waitinglist_stmt = $db->prepare("DELETE luckyconsultation_waitinglist
            FROM luckyconsultation_waitinglist
            JOIN luckyconsultation_dates AS ld
                ON (dates_id = ld.id)
            WHERE
                luckyconsultation_waitinglist.user_id = :user_id
                AND ld.pool = :pool_id"
         );

         $waiting_list_users_stmt = $db->prepare("SELECT  luckyconsultation_waitinglist.*
            FROM luckyconsultation_waitinglist
            JOIN luckyconsultation_dates AS ld
                ON (dates_id = :date_id)"
        );

        // set to default system language
        setTempLanguage();

        // get all pools to dra lots for
        $pools = Pools::findBySQL('lots_drawn = 0 AND date <= NOW()');

        $templates = [];
        foreach(Templates::findBySQL(1) as $t) {
            $templates[$t->id] = $t->template;
        }

        // fixate all waiting lists for later reference
        foreach ($pools as $pool) {
            foreach ($pool->dates as $date) {
                $list = $date->waitinglist->toArray();

                if (!empty($list)) {
                    $waitinglist = [];

                    foreach ($list as $entry) {
                        $waitinglist[] = get_fullname($entry['user_id']) . ' ('. get_username($entry['user_id']) .')';
                    }

                    $history = $date->history;
                    $history[date('H:i:s_d.m.Y')] = $waitinglist;
                    $date->history = $history;

                    $date->store();
                }
            }
        }

        // Sort pools by date (earliest first)
        usort($pools, function($a, $b) {
            return strtotime($a->date) - strtotime($b->date);
        });

        foreach ($pools as $pool) {
            // Sort dates by number of waitlist entries (fewest first)
            $dates = [];

            foreach ($pool->dates as $pool_date) {
                $zw = $pool_date->toArray();
                $zw['watinglist'] = $pool_date->waitinglist->toArray();
                $zw['pool_date']  = $pool_date;
                $dates[] = $zw;
            }

            usort($dates, function($a, $b) {
                return count($a['waitinglist']) - count($b['waitinglist']);
            });

            foreach ($dates as $date_array) {
                $date = $date_array['pool_date'];

                $waiting_list_users_stmt->execute([
                    ':date_id' => $date->id
                ]);
                $list = $waiting_list_users_stmt->fetchAll();

                if (!empty($list)) {
                    // Filter out users who already have an assignment that conflicts with this date
                    $available_users = array_filter($list, function($user) use ($date) {
                        return !$this->hasConflictingAssignment($user['user_id'], $date);
                    });

                    if (!empty($available_users)) {
                        $num = random_int(0, count($available_users) - 1);
                        $winner = array_values($available_users)[$num];

                        echo "Date: ({$date->id}) {$date->start}, Winner is: {$winner['user_id']}, Lot number: $num\n";

                        $date->user_id = $winner['user_id'];
                        $date->store();

                        $clear_waitinglist_stmt->execute([
                            ':user_id' => $winner['user_id'],
                            ':pool_id'    => $date->pool
                        ]);

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
            }

            $pool->lots_drawn = 1;
            $pool->store();
        }
    }

    /**
     * Checks if a user already has an assignment that conflicts with a given date.
     *
     * @param int $user_id The ID of the user to check.
     * @param object $date The date object representing the date to check for conflicts.
     *
     * @return bool Returns true if a conflict is found, false otherwise.
     */
    private function hasConflictingAssignment($user_id, $date)
    {
        // Check if the user already has an assignment that conflicts with this date
        $existing_assignments = Dates::findBySQL('user_id = ? AND start BETWEEN ? AND ?', [
            $user_id,
            date('Y-m-d H:i:s', strtotime($date->start) - 3600), // 1 hour before
            date('Y-m-d H:i:s', strtotime($date->start) + 3600)  // 1 hour after
        ]);

        return count($existing_assignments) > 0;
    }
}
