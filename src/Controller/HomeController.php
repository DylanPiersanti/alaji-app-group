<?php

namespace App\Controller;

use App\Api\MoodleApi;
use App\Entity\Quiz;
use App\Entity\Candidate;
use App\Controller\MoodleApiController;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

class HomeController extends AbstractController
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
        $quiz = $this->getDoctrine()
            ->getRepository(Quiz::class)
            ->findAll();

        return $this->render('home/quiz.html.twig', [
            'quiz' => $quiz,
        ]);
    }

    /**
     * @Route("/quiz/candidates", name="candidates")
     */

     public function candidat()
     {

         $candidat = $this->getDoctrine()
             ->getRepository(Candidate::class)
             ->findAll();

         return $this->render('home/candidates.html.twig', [
             'candidat' => $candidat,
         ]);
     }


}
