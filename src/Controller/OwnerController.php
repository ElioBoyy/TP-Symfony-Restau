<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class OwnerController extends AbstractController
{
    #[Route('/owner/dashboard', name: 'app_owner_dashboard')]
    public function index(): Response
    {
        return $this->render('owner/dashboard.html.twig');
    }
}
