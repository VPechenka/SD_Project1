<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

final class AnonimActionController extends AbstractController
{
    #[Route('/', name: 'app_anonim_action')]
    public function index(): Response
    {
        return $this->render('anonim_action/homepage.html.twig', [
            'controller_name' => 'AnonimActionController',
        ]);
    }
}
