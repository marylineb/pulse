<?php

namespace App\Controller;

use App\Entity\ChatRoom;
use App\Entity\Message;
use App\Entity\RevealRequest;
use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[Route('/chat')]
#[IsGranted('ROLE_USER')]
class ChatController extends AbstractController
{
    private const MAX_MESSAGE_LENGTH = 2000;

    #[Route('/{id}', name: 'app_chat_show', methods: ['GET', 'POST'])]
    public function show(
        ChatRoom $chatRoom,
        Request $request,
        EntityManagerInterface $entityManager
    ): Response {
        $user = $this->getUser();

        if (!$user instanceof User) {
            throw $this->createAccessDeniedException();
        }

        if ($chatRoom->getUserOne() !== $user && $chatRoom->getUserTwo() !== $user) {
            throw $this->createAccessDeniedException('Tu ne peux pas accéder à cette discussion.');
        }

        if ($request->isMethod('POST')) {
            // Jeton CSRF : empêche un autre site d'envoyer un message à ta place.
            if (!$this->isCsrfTokenValid('chat_message'.$chatRoom->getId(), $request->getPayload()->getString('_token'))) {
                throw $this->createAccessDeniedException('Jeton de sécurité invalide.');
            }

            $content = trim($request->getPayload()->getString('content'));

            if ($content === '') {
                $this->addFlash('error', 'Ton message est vide.');
            } elseif (mb_strlen($content) > self::MAX_MESSAGE_LENGTH) {
                $this->addFlash('error', 'Ton message dépasse '.self::MAX_MESSAGE_LENGTH.' caractères.');
            } else {
                $message = new Message();
                $message->setRoom($chatRoom);
                $message->setAuthor($user);
                $message->setContent($content);
                $message->setIsFlagged(false);
                $message->setCreatedAt(new \DateTimeImmutable());

                $entityManager->persist($message);
                $entityManager->flush();
            }

            return $this->redirectToRoute('app_chat_show', [
                'id' => $chatRoom->getId(),
            ]);
        }

        $messages = $entityManager->getRepository(Message::class)->findBy(
            ['room' => $chatRoom],
            ['createdAt' => 'ASC']
        );

        $otherUser = $chatRoom->getUserOne() === $user ? $chatRoom->getUserTwo() : $chatRoom->getUserOne();
        $otherProfile = $otherUser?->getProfile();

        $revealRequest = $entityManager->getRepository(RevealRequest::class)->findOneBy([
            'room' => $chatRoom,
        ]);

        return $this->render('chat/index.html.twig', [
            'chatRoom' => $chatRoom,
            'messages' => $messages,
            'otherUser' => $otherUser,
            'otherProfile' => $otherProfile,
            'revealRequest' => $revealRequest,
            'maxLength' => self::MAX_MESSAGE_LENGTH,
        ]);
    }
}
