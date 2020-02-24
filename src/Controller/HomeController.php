<?php

namespace App\Controller;

use App\Api\MoodleApi;
use App\Controller\MoodleApiController;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

class HomeController extends AbstractController
{
    /**
     * @Route("/", name="home")
     */
    public function index(Request $request, MoodleApiController $moodleApiController, MoodleApi $moodleApi)
    {
        if ($request->isMethod('POST')) {
            $moodleApiController->postQuiz($moodleApi);
            $moodleApiController->postCandidate($moodleApi);
            $moodleApiController->postCriteria($moodleApi);
            $moodleApiController->postResult($moodleApi);
            $moodleApiController->postcoef($moodleApi);
        }
        return $this->render('home/index.html.twig', [
            'controller_name' => 'HomeController',
        ]);
    }
}
