<?php
namespace App\Api;


use Symfony\Component\Dotenv\Dotenv;
use Symfony\Component\HttpClient\HttpClient;

use \DOMDocument;
use \DomXPath;

class MoodleApi extends AbstractApi
{

    public function getTeacher(string $email)
    {
        return $this->call('core_user_get_users', [
            'criteria[0][key]' => 'email',
            'criteria[0][value]' => $email
        ]);
    }

    public function getCourses(int $id)
    {
        return $this->call('core_group_get_course_user_groups', [
            'userid' => $id
        ]);
    }

    public function getQuiz(int $id)
    {
        return $this->call('mod_quiz_get_quizzes_by_courses', [
            'courseids[]' => $id
        ]);
    }

    public function getUsers(int $id)
    {
        return $this->call('core_enrol_get_enrolled_users', [
            'courseid' => $id
        ]);
    }

    public function getAttempsUser(int $idQuiz, int $idCandidate)
    {
        return $this->call('mod_quiz_get_user_attempts', [
            'quizid' => $idQuiz,
            'userid' => $idCandidate
        ]);
    }

    public function getAttempsReview(int $id)
    {
        return $this->call('mod_quiz_get_attempt_review', [
            'attemptid' => $id,
        ]);
    }

    public function getNameQuestion(string $name)
    {
        $names = mb_convert_encoding($name, 'HTML-ENTITIES', 'UTF-8');
        $dom = @DOMDocument::loadHTML($names);

        $finder = new DomXPath($dom);
        $classname="qtext";
        $nodes = $finder->query("//*[contains(concat(' ', normalize-space(@class), ' '), ' $classname ')]");
        return $nodes->item(0)->nodeValue;

    }

    public function averageResult(int $elearning, float $coefelearning, int $oral, float $coeforal)
    {
        $array = [[$elearning, $coefelearning], [$oral, $coeforal]];
        $nbElements = count($array);
        $sum = 0;
        $coef = 0;
        for ($i=0; $i < $nbElements; $i++) {
          $sum = $sum + ($array[$i][0] * $array[$i][1]);
          $coef = $coef + $array[$i][1];
        }
        return ($sum/$coef) * 100;
    }

    function acquis(float $average)
    {
        if ($average>=50) {
            $response = "Acquis";
        }else {
            $response = "Non acquis";
        }
        return $response;
    }

}
