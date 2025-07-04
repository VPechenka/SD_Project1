<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

final class UserActionController extends AbstractController
{
    #[Route('/user/action', name: 'app_user_action')]
    public function index(): Response
    {
        return $this->render('user_action/post.html.twig', [
            'controller_name' => 'UserActionController',
        ]);
    }
}
