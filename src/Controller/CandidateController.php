<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;
use App\Entity\Candidate;
use App\Entity\Result;


class CandidateController extends AbstractController
{

     /**
     * @Route("/quiz/candidates", name="candidates")
     */

    public function candidat()
    {

       $user = $this->getUser();

       return $this->render('candidate/candidates.html.twig', [
           'teacher' => $user,
       ]);
    }


    /**
     * @Route("/quiz/candidates/{id}", name="candidate_profil")
     */
    public function index(int $id)
    {

        $candidate = $this->getDoctrine()->getRepository(Candidate::class)->find($id);

        $results = $this->getDoctrine()->getRepository(Result::class)->findBy(['candidate' => $candidate]);

        return $this->render('candidate/index.html.twig', [
            'candidate' => $candidate,
            'results' => $results
        ]);
    }
}
