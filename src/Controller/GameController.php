<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[Route('/games')]
#[IsGranted('ROLE_USER')]
class GameController extends AbstractController
{
    #[Route('/{slug}', name: 'app_game_show')]
    public function show(string $slug): Response
    {
        $titles = [
            'questions-duo' => 'Questions Duo',
            'cartes-mystere' => 'Cartes Mystère',
            'tu-preferes' => 'Tu préfères',
        ];

        if (!isset($titles[$slug])) {
            throw $this->createNotFoundException('Jeu introuvable.');
        }

        return $this->render('game/index.html.twig', [
            'slug' => $slug,
            'title' => $titles[$slug],
        ]);
    }
}