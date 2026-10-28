<?php

namespace App\Controller;

use App\Entity\ChatRoom;
use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[Route('/match')]
#[IsGranted('ROLE_USER')]
class MatchController extends AbstractController
{
    #[Route('/', name: 'app_match_list')]
    public function index(EntityManagerInterface $em): Response
    {
        $user = $this->getUser();

        if (!$user instanceof User) {
            throw $this->createAccessDeniedException();
        }

        $rooms = $em->getRepository(ChatRoom::class)->createQueryBuilder('r')
            ->where('r.userOne = :user OR r.userTwo = :user')
            ->setParameter('user', $user)
            ->getQuery()
            ->getResult();

        return $this->render('match/index.html.twig', [
            'rooms' => $rooms,
            'currentUser' => $user,
        ]);
    }
}