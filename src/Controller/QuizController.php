<?php

namespace App\Controller;

use App\Api\MoodleApi;
use App\Entity\Quiz;
use App\Entity\Candidate;
use App\Controller\MoodleApiController;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

class QuizController extends AbstractController
{
    /**
     * @Route("/quiz", name="quiz")
     */
    public function quiz(Request $request)
    {
        if ($request->isMethod('POST')) {
            $moodleApiController->postQuiz($moodleApi);
            $moodleApiController->postCandidate($moodleApi);
            $moodleApiController->postCriteria($moodleApi);
            $moodleApiController->postResult($moodleApi);
            $moodleApiController->postcoef($moodleApi);
        }
        $user = $this->getUser();

        return $this->render('home/quiz.html.twig', [
            'teacher' => $user,
        ]);
    }

    /**
     * @Route("/quiz/candidates", name="candidates")
     */

     public function candidat()
     {

        $user = $this->getUser();

        return $this->render('home/candidates.html.twig', [
            'teacher' => $user,
        ]);
     }


}
