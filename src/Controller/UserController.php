<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Doctrine\ORM\EntityManagerInterface;
use App\Entity\User;

class UserController extends AbstractController
{
    #[Route('/user/dashboard', name: 'app_user_dashboard')]
    public function index(): Response
    {
        if (!$this->getUser()) {
            return $this->redirectToRoute('app_login');
        }
        if (!in_array('ROLE_USER', $this->getUser()->getRoles())) {
            return $this->redirectToRoute('app_error_unauthorized');
        }
        
        return $this->render('user/dashboard.html.twig');
    }

    #[Route('/newsletter_subscribe', name: 'app_newsletter_subscribe')]
    public function newsletterSubscribe(EntityManagerInterface $entityManager): Response
    {
        /*$email = $this->getUser()->getEmail();
        $user = $entityManager->getRepository(User::class)->findOneBy(['email' => $email]);

        $user = $this->getUser();
        if (!$user) {
            return $this->redirectToRoute('app_login');
        }

        $user->setNewsletter(true);
        $entityManager = $this->getDoctrine()->getManager();
        $entityManager->persist($user);
        $entityManager->flush();*/

        return $this->redirectToRoute('app_user_dashboard');
    }
}
