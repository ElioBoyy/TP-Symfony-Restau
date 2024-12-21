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
        if ($this->getUser()) {
            return $this->redirectToRoute('app_error_already_logged_in');
        }

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
            $user->setIsNewsletter($request->request->get('newsletter') === 'on');
            $user->setRoles(['ROLE_USER']);
            $user->setIsActive(false);

            $user->setPassword($passwordHasher->hashPassword(
                $user,
                $request->request->get('password')
            ));

            $entityManager->persist($user);
            $entityManager->flush();

            return $this->redirectToRoute('app_confirm');
        }

        return $this->render('security/register.html.twig');
    }

    #[Route('/confirm', name: 'app_confirm')]
    public function confirm(): Response
    {
        return $this->render('security/registration_confirmation.html.twig');
    }

    #[Route(path: '/logout', name: 'app_logout')]
    public function logout(): void
    {
        throw new \LogicException('This method can be blank - it will be intercepted by the logout key on your firewall.');
    }

    #[Route('/reset_password', name: 'app_forgot_password_request')]
    public function forgotPasswordRequest(): Response
    {
        return $this->render('security/forgot_password_request.html.twig');
    }

    #[Route('/reset_password/{token}', name: 'app_reset_password')]
    public function resetPassword(Request $request, string $token, UserPasswordHasherInterface $passwordHasher, EntityManagerInterface $entityManager): Response
    {
        $user = $entityManager->getRepository(User::class)->findOneBy(['resetToken' => $token]);

        if (!$user) {
            throw $this->createNotFoundException('Invalid token');
        }

        if ($request->isMethod('POST')) {
            $newPassword = $request->request->get('password');
            $user->setPassword($passwordHasher->hashPassword($user, $newPassword));
            $user->setResetToken(null);

            $entityManager->persist($user);
            $entityManager->flush();

            return $this->redirectToRoute('app_login');
        }

        return $this->render('security/reset_password.html.twig', ['token' => $token]);
    }

    public function hashPassword(UserPasswordHasherInterface $passwordHasher, User $user, string $plainPassword): void
    {
        $hashedPassword = $passwordHasher->hashPassword($user, $plainPassword);
        $user->setPassword($hashedPassword);
    }
}
