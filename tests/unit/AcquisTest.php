<?php
namespace App\Tests;
use App\Api\MoodleApi;

class AcquisTest extends \Codeception\Test\Unit
{
    /**
     * @var \App\Tests\UnitTester
     */
    protected $tester;

    protected function _before()
    {
    }

    protected function _after()
    {
    }

    // tests
    public function testAcquis()
    {
        $moodleApi = new MoodleApi;
        $result = $moodleApi->acquis(60);
        $this->assertEquals("Acquis", $result);
    }
    public function testNotAcquis()
    {
        $moodleApi = new MoodleApi;
        $result = $moodleApi->acquis(40);
        $this->assertEquals("Non acquis", $result);
    }
}
