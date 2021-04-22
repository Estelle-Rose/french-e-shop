<?php

namespace App\Controller;

use App\Classes\MailJet;
use App\Entity\User;
use App\Form\RegisterType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

class RegisterController extends AbstractController
{
    /**
     * @Route("/inscription", name="register")
     */
    public function index(Request $request, EntityManagerInterface $em, UserPasswordEncoderInterface $userPasswordEncoder): Response

    {
        $notification = null;
        $user = new User();
        $form = $this->createForm(RegisterType::class, $user);
        $form->handleRequest($request);
        if($form->isSubmitted() && $form->isValid()) {
            $search_email = $em->getRepository(User::class)->findOneByEmail($user->getEmail());
            if(!$search_email) {
                $password = $userPasswordEncoder->encodePassword($user, $user->getPassword());
                $user->setPassword($password);
                $em->persist($user);
                $em->flush();
                $mail = new MailJet();
                $content = 'Bonjour '.$user->getFullname().', votre inscription a bien été enregistrée';
                $mail->send($user->getEmail(), $user->getFullname(),'Bienvenue à la boutique française', $content);

                $this->addFlash('success', 'Votre inscription est bien enregistrée. Vous pouvez vous connecter pour accéder à votre compte.');
                $this->redirectToRoute('app_login');
            } else {
                $this->addFlash('warning', 'Cette adresse mail est déjà utilisée.');

            }

        }
        return $this->render('register/index.html.twig', [
            'form' => $form->createView()

        ]);
    }
}