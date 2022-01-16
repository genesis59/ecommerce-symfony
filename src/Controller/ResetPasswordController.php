<?php

namespace App\Controller;

use App\Classe\Mail;
use App\Entity\ResetPassword;
use App\Entity\User;
use App\Form\ResetPasswordType;
use DateTime;
use DateTimeImmutable;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

class ResetPasswordController extends AbstractController
{

    public function __construct(private EntityManagerInterface $em)
    {
     
    }

    #[Route('/mot-de-passe-oublie', name: 'reset_password')]
    public function index(Request $request): Response
    {
        if($this->getUser()){
            return $this->redirectToRoute('home');
        }

        if($request->get('email')){
            $user = $this->em->getRepository(User::class) ->findOneBy(['email' => $request->get('email')]);
            if($user){
                // 1: Enregistrer la demande
                $resetPassword = new ResetPassword();
                $resetPassword->setUser($user);
                $resetPassword->setToken(uniqid());
                $resetPassword->setCreatedAt(new DateTimeImmutable());
                $this->em->persist($resetPassword);
                $this->em->flush();

                // 2: envoi email
                $mail = new Mail();
                $url = $this->generateUrl('update_password',['token' => $resetPassword->getToken()],UrlGeneratorInterface::ABSOLUTE_URL);
                $content = "Bonjour" . $user->getFirstname() . ',<br/>Vous avez demander de réinitialiser votre mot de passe sur le site MyEcommerce.<br/><br/>';
                $content .= 'Merci de cliquer sur le lien suivant pour <a href="'.$url.'">mettre à jour votre mot de passe</a>.';
                $mail->send($user->getEmail(),$user->getFirstname() .' '. $user->getLastname(),'Réinitialisation de votre mot de passe',$content);
                $this->addFlash('notice','Vous allez recevoir un email pour réinitialiser votre mot de passe.');
           
            } else {
                $this->addFlash('notice','Cette adresse email est inconnue.');
            }
        }
        return $this->render('reset_password/index.html.twig');
    }

    #[Route('/modifier-mon-mot-de-passe/{token}', name: 'update_password')]
    public function update($token,Request $request,UserPasswordHasherInterface $encoder): Response
    {
        $resetPassword = $this->em->getRepository(ResetPassword::class)->findOneBy(['token' => $token]);

        if(!$resetPassword){
            return $this->redirectToRoute('reset_password');
        }
        $now = new DateTime();
        if($now > $resetPassword->getCreatedAt()->modify('+ 3 hour')){
            $this->addFlash('notice','Votre demande de mot de passe a expiré. Merci de la renouveler.');
            return $this->redirectToRoute('reset_password');
        }

        $form = $this->createForm(ResetPasswordType::class);
        $form->handleRequest($request);
        if($form->isSubmitted() && $form->isValid()){
            $user = $resetPassword->getUser();
            $user->setPassword($encoder->hashPassword($user,$form->get('new_password')->getData()));
            $this->em->flush();
            $this->addFlash('notice','Votre mot de passe a bien été mis à jour.');
            $this->redirectToRoute('app_login');
        }
        return $this->render('reset_password/update.html.twig',[
            'form' => $form->createView()
        ]);
    }
}
