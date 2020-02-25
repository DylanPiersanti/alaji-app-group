<?php

namespace App\Controller\Admin;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;
use App\Entity\User;

class AdminController extends AbstractController
{
    /**
     * @Route("/admin", name="admin_profil")
     */
    public function teachersList()
    {
        $teachers = $this->getDoctrine()->getRepository(User::class)->findAll();
        
        return $this->render('admin/profil.html.twig', [
            'teachers' => $teachers,
        ]);
    }
}
