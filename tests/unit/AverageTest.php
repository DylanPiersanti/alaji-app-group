<?php
namespace App\Tests;
use App\Api\MoodleApi;

class AverageTest extends \Codeception\Test\Unit
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
    public function testAverage()
    {
        $moodleApi = new MoodleApi;
        $result = $moodleApi->averageResult(1, 0.77, 0, 0.23);
        $this->assertEquals(77, $result);
    }
}
