<?php

namespace App\Controller;

use App\Form\ChangePasswordType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Annotation\Route;

class AccountPasswordController extends AbstractController
{
    private $em;

    public function __construct(EntityManagerInterface $entityManagerInterface){
        $this->em = $entityManagerInterface;
    }
    
    #[Route('/compte/modifier-mot-de-passe', name: 'account_password')]
    public function index(Request $request, UserPasswordHasherInterface $encoder): Response
    {
        $notification = null;
        $form = $this->createForm(ChangePasswordType::class, $this->getUser());
        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid()){
            $user = $form->getData();
            if($encoder->isPasswordValid($user,$form->get('old_password')->getData())){
                $user->setPassword($encoder->hashPassword($user,$form->get('new_password')->getData()));
                $this->em->flush();
                $notification = "Votre mot de passe a bien été mis à jour";
            } else {
                $notification = "Votre mot de passe actuel est erroné";
            }     
        }

        return $this->render('account/password.html.twig', [
            'form' => $form->createView(),
            'notification' => $notification
        ]);
    }
}
