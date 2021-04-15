<?php

namespace App\Controller;

use App\Form\ChangePasswordType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

class AccountPasswordController extends AbstractController
{
    /**
     * @Route("/mon-compte/modifier-mot-de-passe", name="account_password")
     */
    public function index(Request $request, EntityManagerInterface $entityManager, UserPasswordEncoderInterface $encoder): Response
    {
        $notification = null;

        $user = $this->getUser(); // on récupère le user
        $form =$this->createForm(ChangePasswordType::class, $user);
        $form->handleRequest($request);
        if($form->isSubmitted() && $form->isValid()) {
        $old_password = $form->get('old_password')->getData(); // on récupère l'ancien mot de passe
        //on compare old_password avec le mot de passe du user
            if($encoder->isPasswordValid($user, $old_password )) {
                $new_password = $form->get('new_password')->getData(); // on récupère le nouveau mot de passe
                $password = $encoder->encodePassword($user,$new_password); // on crypte le nouveau mot de passe
                $user->setPassword($password); // on met à jour le user et on envoie en bdd
                // la methode persist() n'est pas nécessaire pour update
                $entityManager->flush();
                $notification  = 'Votre mot de passe a bien été mis à jour';
                $this->redirectToRoute('account');
            }
            else {
                $notification  = "Votre mot de passe n'est pas valide";
            }
        }
        return $this->render('account/password.html.twig',[
            'form' => $form->createView(),
            'notification' => $notification
        ]);
    }
}
