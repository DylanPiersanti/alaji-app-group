<?php

namespace App\Controller\Admin;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;

class AdminController extends AbstractController
{
    /**
     * @Route("/admin", name="admin_profil")
     */
    public function index()
    {
        return $this->render('admin/profil.html.twig', [
            'controller_name' => 'AdminController',
        ]);
    }
}
