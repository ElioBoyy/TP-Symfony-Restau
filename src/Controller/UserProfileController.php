<?php

namespace App\Controller;

use App\Form\UserProfileType;
use App\Form\RoleUpgradeRequestType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Email;

class UserProfileController extends AbstractController
{
    #[Route('/profile', name: 'app_user_profile')]
    public function index(Request $request, MailerInterface $mailer, EntityManagerInterface $entityManager): Response
    {
        $user = $this->getUser();
        if (!$user) {
            return $this->redirectToRoute('app_login');
        }

        $form = $this->createForm(UserProfileType::class, $user);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();
            $this->addFlash('success', 'Your profile has been updated.');
            return $this->redirectToRoute('app_user_profile');
        }

        $upgradeForm = $this->createForm(RoleUpgradeRequestType::class);
        $upgradeForm->handleRequest($request);

        if ($upgradeForm->isSubmitted() && $upgradeForm->isValid()) {
            $email = (new Email())
                ->from($user->getUserIdentifier())
                ->to('mathissportiello.pro@gmail.com')
                ->subject('Role Upgrade Request')
                ->text('User email : ' . $user->getUserIdentifier() . ' is requesting an upgrade from ROLE_USER to ROLE_OWNER.' . "\n\n" . 'Please review the request and take appropriate action.');

            $mailer->send($email);

            $this->addFlash('success', 'Your role upgrade request has been sent.');
            return $this->redirectToRoute('app_user_profile');
        }

        return $this->render('user_profile/index.html.twig', [
            'user' => $user,
            'form' => $form->createView(),
            'upgradeForm' => $upgradeForm->createView(),
        ]);
    }
}

