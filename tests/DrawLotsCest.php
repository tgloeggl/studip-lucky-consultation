<?php

class DrawLotsCest
{
    private $dozent_name;
    private $author_name;

    const CRONJOB = 'Losbasierte Sprechstunden: Lost fällige Losungen aus.';

    public function _before(ApiTester $I)
    {
        $config = $I->getConfig();

        $this->dozent_name = $config['dozent_name'];
        $this->dozent_name = $config['course_student'];

        $I->amHttpAuthenticated($config['course_student'], $config['course_student_password']);
    }

    // tests
    public function testUserRoute(ApiTester $I)
    {
        $response = $I->sendGet('/user', [ 'status' => 'pending' ]);
        $I->seeResponseCodeIs(200);
        $I->seeResponseIsJson();

        $I->seeResponseContainsJson([
            'type' => 'user',
            'data' => [
                'username' => $this->dozent_name,
            ]
        ]);
    }

    public function testDrawLots(ApiTester $I)
    {
        // Start cronjobs
        $I->runCronjob(self::CRONJOB);
    }
}
