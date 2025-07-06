<?php
namespace Helper;

// here you can define custom actions
// all public methods declared in helper class will be available in $I

use GuzzleHttp\Client;

class Api extends \Codeception\Module
{
    protected $requiredFields = [
        'dozent_name',
        'dozent_password',
        'course_student',
        'course_id',
        'course_student_password'
    ];

    const STUDIP_CLI = __DIR__ . '/../../../../../../../cli/studip';

    public function getConfig(): array {
        return $this->config;
    }

    /**
     * Run studip cronjob
     *
     * @param string $cronjob cronjob description
     */
    public function runCronjob(string $cronjob)
    {
        if (file_exists(self::STUDIP_CLI)) {
            // Run cronjob on host if studip cli exist
            $studip_cli = self::STUDIP_CLI;
            $command = "php $studip_cli cronjobs:execute $(php $studip_cli cronjobs:list | grep '$cronjob' | awk '{print $1}')";
        } else if ($this->config['docker_command']) {
            $command = str_replace('CRONJOB', $cronjob, $this->config['docker_command']);
        } else {
            // Run cronjob in docker container
            $compose_file = __DIR__ . '/../../../.github/docker/docker-compose.yml';
            $command = "docker compose -f $compose_file exec studip bash -c \"php ./cli/studip cronjobs:execute \\$(php ./cli/studip cronjobs:list | grep '$cronjob' | awk '{print \\$1}')\"";
        }
        exec($command, $output, $result_code);

        $this->assertEquals(0, $result_code, 'Cronjob run successful');
    }
}
