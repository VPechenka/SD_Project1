<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

final class AdminActionController extends AbstractController
{
    #[Route('/admin/action', name: 'app_admin_action')]
    public function index(): Response
    {
        return $this->render('admin_action/post.html.twig', [
            'controller_name' => 'AdminActionController',
        ]);
    }
}
