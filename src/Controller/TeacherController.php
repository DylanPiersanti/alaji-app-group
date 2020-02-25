<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;
use App\Entity\User;

class TeacherController extends AbstractController
{
    /**
     * @Route("/profil", name="teacher_profil")
     */
    public function userProfil()
    {
        $user = $this->getUser();

        $teachers = $this->getDoctrine()->getRepository(User::class)->findBy(
            ['id' => $user]
        );

        return $this->render('teacher/index.html.twig', [
            'teachers' => $teachers,
        ]);
    }
}
