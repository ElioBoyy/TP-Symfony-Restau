<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class AdminController extends AbstractController
{
    #[Route('/dashboard/admin', name: 'app_admin_dashboard')]
    public function index(): Response
    {
        return $this->render('admin/dasbhoard.html.twig');
    }
}
