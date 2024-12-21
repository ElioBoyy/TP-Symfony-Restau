<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class ErrorController extends AbstractController
{
    #[Route('/error/unauthorized', name: 'app_error_unauthorized')]
    public function index(): Response
    {
        return $this->render('error/unauthorized.html.twig');
    }

    #[Route('/error/already_logged_in', name: 'app_error_already_logged_in')]
    public function alreadyLoggedIn(): Response
    {
        return $this->render('error/already_logged_in.html.twig');
    }
}
