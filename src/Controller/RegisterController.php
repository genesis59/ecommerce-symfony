<?php

namespace App\Controller;

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

    private $em;

    public function __construct(EntityManagerInterface $entityManagerInterface)
    {
        $this->em = $entityManagerInterface;
    }

    #[Route('/inscription', name: 'register')]
    public function index(Request $request, UserPasswordHasherInterface $encoder): Response

    {

        $user = new User();
        $form = $this->createForm(RegisterType::class, $user);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            
            $user->setPassword($encoder->hashPassword($form->getData(), $form->getData()->getPassword()));
            $this->em->persist($user);
            $this->em->flush();
        }

        return $this->render('register/index.html.twig', [
            'form' => $form->createView()
        ]);
    }
}
