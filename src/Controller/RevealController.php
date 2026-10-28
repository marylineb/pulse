<?php

namespace App\Controller;

use App\Entity\ChatRoom;
use App\Entity\RevealRequest;
use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[Route('/reveal')]
#[IsGranted('ROLE_USER')]
class RevealController extends AbstractController
{
    // POST uniquement : une action qui modifie des données ne doit jamais passer par un simple lien (GET).
    #[Route('/{id}', name: 'app_reveal_request', methods: ['POST'])]
    public function reveal(
        ChatRoom $chatRoom,
        Request $request,
        EntityManagerInterface $em
    ): Response {
        $user = $this->getUser();

        if (!$user instanceof User) {
            throw $this->createAccessDeniedException();
        }

        // 1. Seules les deux personnes de la discussion peuvent demander ou accepter la révélation.
        if ($chatRoom->getUserOne() !== $user && $chatRoom->getUserTwo() !== $user) {
            throw $this->createAccessDeniedException('Tu ne fais pas partie de cette discussion.');
        }

        // 2. Jeton CSRF : la demande doit venir du bouton de la page de chat, pas d'un site tiers.
        if (!$this->isCsrfTokenValid('reveal'.$chatRoom->getId(), $request->getPayload()->getString('_token'))) {
            throw $this->createAccessDeniedException('Jeton de sécurité invalide.');
        }

        $redirect = $this->redirectToRoute('app_chat_show', ['id' => $chatRoom->getId()]);

        if ($chatRoom->isRevealed()) {
            return $redirect;
        }

        $revealRequest = $em->getRepository(RevealRequest::class)->findOneBy([
            'room' => $chatRoom,
        ]);

        if (!$revealRequest) {
            // Première demande : la personne qui demande accepte d'office, l'autre doit encore accepter.
            $revealRequest = new RevealRequest();
            $revealRequest->setRoom($chatRoom);
            $revealRequest->setRequestedBy($user);
            $revealRequest->setAcceptedByRequester(true);
            $revealRequest->setAcceptedByOther(false);
            $revealRequest->setCreatedAt(new \DateTimeImmutable());

            $em->persist($revealRequest);
            $em->flush();

            $this->addFlash('success', 'Demande de révélation envoyée. Elle sera effective quand l\'autre personne acceptera.');

            return $redirect;
        }

        // 3. La personne qui a fait la demande ne peut pas accepter à la place de l'autre.
        if ($revealRequest->getRequestedBy() === $user) {
            $this->addFlash('error', 'Ta demande est déjà envoyée : c\'est à l\'autre personne d\'accepter.');

            return $redirect;
        }

        // C'est bien l'autre personne qui accepte : les deux ont donné leur accord.
        $revealRequest->setAcceptedByOther(true);
        $revealRequest->setRevealedAt(new \DateTimeImmutable());
        $chatRoom->setIsRevealed(true);

        $em->flush();

        $this->addFlash('success', 'Vous avez accepté tous les deux : vos identités sont révélées 💘');

        return $redirect;
    }
}
