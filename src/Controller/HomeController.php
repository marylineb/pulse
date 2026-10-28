<?php

namespace App\Controller;

use App\Entity\ChatRoom;
use App\Entity\Crush;
use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

final class HomeController extends AbstractController
{
    #[Route('/', name: 'app_home')]
    public function index(EntityManagerInterface $entityManager): Response
    {
        $user = $this->getUser();

        if (!$user instanceof User) {
            return $this->render('home/index.html.twig');
        }

        $crushRepository = $entityManager->getRepository(Crush::class);
        $chatRoomRepository = $entityManager->getRepository(ChatRoom::class);

        $myCrushes = $crushRepository->findBy(
            ['sender' => $user],
            ['createdAt' => 'DESC']
        );

        $myMatches = $crushRepository->findBy(
            ['sender' => $user, 'isMutual' => true],
            ['createdAt' => 'DESC']
        );

        $chatRooms = $chatRoomRepository->createQueryBuilder('r')
            ->where('r.userOne = :user OR r.userTwo = :user')
            ->setParameter('user', $user)
            ->orderBy('r.createdAt', 'DESC')
            ->getQuery()
            ->getResult();

        $games = [
            [
                'title' => 'Questions Duo',
                'slug' => 'questions-duo',
                'type' => 'duo',
                'description' => 'Des questions pour mieux vous découvrir à deux.',
                'status' => 'Disponible',
            ],
            [
                'title' => 'Cartes Mystère',
                'slug' => 'cartes-mystere',
                'type' => 'solo',
                'description' => 'Retourne des cartes et découvre de petites surprises.',
                'status' => 'Disponible',
            ],
            [
                'title' => 'Tu préfères',
                'slug' => 'tu-preferes',
                'type' => 'duo',
                'description' => 'Mini-jeu fun pour créer de la complicité.',
                'status' => 'Disponible',
            ],
        ];

        return $this->render('home/dashboard.html.twig', [
            'user' => $user,
            'myCrushes' => $myCrushes,
            'myMatches' => $myMatches,
            'chatRooms' => $chatRooms,
            'games' => $games,
        ]);
    }
}