<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class DiscordController extends AbstractController
{
    #[Route('/discord', name: 'app_discord')]
    public function index(): Response
    {
        return $this->render('page/discord/index.html.twig', [
            'controller_name' => 'DiscordController',
        ]);
    }
}
