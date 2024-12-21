<?php

namespace App\Controller;

use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;

class SecurityController extends AbstractController
{
    #[Route(path: '/login', name: 'app_login')]
    public function login(AuthenticationUtils $authenticationUtils): Response
    {
        // if ($this->getUser()) {
        //     return $this->redirectToRoute('target_path');
        // }

        // get the login error if there is one
        $error = $authenticationUtils->getLastAuthenticationError();
        // last username entered by the user
        $lastUsername = $authenticationUtils->getLastUsername();

        return $this->render('security/login.html.twig', ['last_username' => $lastUsername, 'error' => $error]);
    }

    #[Route('/register', name: 'app_register')]
    public function register(Request $request, UserPasswordHasherInterface $passwordHasher, EntityManagerInterface $entityManager): Response
    {
        $email = $request->request->get('email');
        $existingUser = $entityManager->getRepository(User::class)->findOneBy(['email' => $email]);

        if ($existingUser && !$existingUser->isActive()) {
            return $this->redirectToRoute('app_confirm', ['user' => $existingUser->getEmail()]);
        }

        $user = new User();

        if ($request->isMethod('POST')) {
            $user->setName($request->request->get('name'));
            $user->setEmail($email);
            $user->setPhone($request->request->get('phone'));
            $user->setCountry($request->request->get('country'));
            $user->setAddress($request->request->get('address'));
            $user->setZipCode($request->request->get('zipCode'));
            $user->setRoles(['ROLE_USER']);
            $user->setActive(false);

            $user->setPassword($passwordHasher->hashPassword(
                $user,
                $request->request->get('password')
            ));

            $entityManager->persist($user);
            $entityManager->flush();
        }

        return $this->render('security/register.html.twig');
    }

    #[Route(path: '/logout', name: 'app_logout')]
    public function logout(): void
    {
        throw new \LogicException('This method can be blank - it will be intercepted by the logout key on your firewall.');
    }
}
