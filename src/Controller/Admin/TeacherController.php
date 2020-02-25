<?php

namespace App\Controller\Admin;


use App\Api\MoodleApi;
use App\Controller\MoodleApiController;
use App\Entity\User;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;

class TeacherController extends AbstractController
{
    /**
     * @Route("/admin/add", name="add_teacher")
     */
    public function addUser(Request $request, UserPasswordEncoderInterface $encoder, MoodleApiController $moodleApiController, MoodleApi $moodleApi )
    {

        $user = new User;
        $form = $this->createFormBuilder($user)
            ->add('email', EmailType::class)
            ->add('password', PasswordType::class)
            ->add('submit', SubmitType::class)
            ->getForm();

        $form->handleRequest($request);
        $message = null;
        if($form->isSubmitted() && $form->isValid()) {
            $email = $user->getEmail();
            $response = $moodleApiController->postTeacher($moodleApi, $email);

            if ($response['success']) {
                $fullname = $response['fullname'];
                $idMoodle = $response['idMoodle'];
                $passwordEncoded = $encoder->encodePassword($user, $user->getPassword());
                $user->setPassword($passwordEncoded);
                $user->setRoles(["ROLE_USER"]);
                $user->setFullname($fullname);
                $user->setMoodleId($idMoodle);
                $em = $this->getDoctrine()->getManager();
                $em->persist($user);
                $em->flush();

                return $this->redirectToRoute('admin_profil');
            }
            $message = $response['message'];
        }



        return $this->render('admin/add.html.twig', [
            'form' => $form->createView(),
            'message' => $message
        ]);
    }
}
