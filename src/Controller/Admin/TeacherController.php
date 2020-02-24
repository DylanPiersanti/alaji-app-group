<?php

namespace App\Controller\Admin;

use App\Entity\User;

use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;

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
    public function addUser(Request $request, UserPasswordEncoderInterface $encoder)
    {

        $user = new User;
        $form = $this->createFormBuilder($user)
            ->add('email', EmailType::class)
            ->add('fullname', TextType::class)
            ->add('password', PasswordType::class)
            ->add('moodle_id', NumberType::class)
            ->add('submit', SubmitType::class)
            ->getForm();

            $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid()) {

            $passwordEncoded = $encoder->encodePassword($user, $user->getPassword());
            $user->setPassword($passwordEncoded);
            $em = $this->getDoctrine()->getManager();
            $em->persist($user);
            $em->flush();

            return $this->redirectToRoute('home');
        }

        

        return $this->render('admin/add.html.twig', [
            'form' => $form->createView()
        ]);
    }
}
