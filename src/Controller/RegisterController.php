<?php

namespace App\Controller;

use App\Classe\Mail;
use App\Entity\User;
use App\Form\RegisterType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Annotation\Route;

class RegisterController extends AbstractController
{
    public function __construct(private EntityManagerInterface $em)
    {
    }

    #[Route('/inscription', name: 'register')]
    public function index(Request $request, UserPasswordHasherInterface $encoder): Response
    {
        $notification = null;
        $user = new User();
        $form = $this->createForm(RegisterType::class, $user);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            $search_email = $this->em->getRepository(User::class)->findOneBy(['email' => $form->getData()->getEmail()]);

            if (!$search_email) {

                $user->setPassword($encoder->hashPassword($form->getData(), $form->getData()->getPassword()));
                $this->em->persist($user);
                $this->em->flush();

                $mail = new Mail();
                $content = "Inscription réussie";
                $mail->send($user->getEmail(), $user->getFirstname(), "Bienvenue sur MyEcommerce", $content);
                $notification = "Votre inscription s'est correctement déroulée. Vous pouvez dés à présent vous connecter à votre compte";
                return $this->redirectToRoute('login');
            } else {
                $notification = "L'email que vous avez renseigné existe déjà";
            }
        }

        return $this->render('register/index.html.twig', [
            'form' => $form->createView(),
            'notification' => $notification
        ]);
    }
}
