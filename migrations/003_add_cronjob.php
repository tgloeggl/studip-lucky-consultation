<?php

class AddCronjob extends Migration
{
    const BASE_DIR = 'public/plugins_packages/gundk.it/LuckyConsultation/cronjobs/';

    public function description()
    {
        return 'Remove old cronjobs and add new video discovery and worker cronjob';
    }

    public function up()
    {
        $scheduler = CronjobScheduler::getInstance();

        // add video discovery cronjob
        if (!$task_id = CronjobTask::findByFilename(self::BASE_DIR . 'lc_draw_lots.php')[0]->task_id) {
            $task_id =  $scheduler->registerTask(self::BASE_DIR . 'lc_draw_lots.php', true);
        }

        // add the new cronjob
        if ($task_id) {
            $scheduler->cancelByTask($task_id);
            $scheduler->schedulePeriodic($task_id, -5);  // negative value means "every x minutes"
            CronjobSchedule::findByTask_id($task_id)[0]->activate();
        }
    }

    function down()
    {
        $scheduler = CronjobScheduler::getInstance();

        if ($task_id = CronjobTask::findByFilename(self::BASE_DIR . 'lc_draw_lots.php')[0]->task_id) {
            $scheduler->cancelByTask($task_id);
            $scheduler->unregisterTask($task_id);
        }
    }
}
