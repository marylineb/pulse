<?php

namespace App\Controller;

use App\Entity\ChatRoom;
use App\Entity\Crush;
use App\Entity\Notification;
use App\Entity\User;
use App\Form\CrushFormType;
use App\Service\CompatibilityService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[Route('/crush')]
#[IsGranted('ROLE_USER')]
class CrushController extends AbstractController
{
    #[Route('/new', name: 'app_crush_new')]
    public function new(
        Request $request,
        EntityManagerInterface $entityManager,
        CompatibilityService $compatibilityService
    ): Response {
        $user = $this->getUser();

        if (!$user instanceof User) {
            throw $this->createAccessDeniedException('Utilisateur non authentifié.');
        }

        $form = $this->createForm(CrushFormType::class, null, [
            'current_user' => $user,
        ]);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            /** @var User|null $target */
            $target = $form->get('target')->getData();

            if (!$target instanceof User) {
                $this->addFlash('error', 'Aucun utilisateur sélectionné.');
                return $this->redirectToRoute('app_crush_new');
            }

            if ($target === $user) {
                $this->addFlash('error', 'Tu ne peux pas te choisir toi-même.');
                return $this->redirectToRoute('app_crush_new');
            }

            $existingCrush = $entityManager->getRepository(Crush::class)->findOneBy([
                'sender' => $user,
                'target' => $target,
            ]);

            if ($existingCrush) {
                $this->addFlash('error', 'Tu as déjà envoyé un crush à cette personne.');
                return $this->redirectToRoute('app_crush_new');
            }

            $senderProfile = $user->getProfile();
            $targetProfile = $target->getProfile();

            $compatibility = 0;
            if ($senderProfile && $targetProfile) {
                $compatibility = $compatibilityService->calculate($senderProfile, $targetProfile);
            }

            $crush = new Crush();
            $crush->setSender($user);
            $crush->setTarget($target);
            $crush->setStatus(Crush::STATUS_PENDING);
            $crush->setIsMutual(false);

            $reverseCrush = $entityManager->getRepository(Crush::class)->findOneBy([
                'sender' => $target,
                'target' => $user,
            ]);

            if ($reverseCrush) {
                $crush->setStatus(Crush::STATUS_MUTUAL);
                $crush->setIsMutual(true);

                $reverseCrush->setStatus(Crush::STATUS_MUTUAL);
                $reverseCrush->setIsMutual(true);

                $existingRoom = $entityManager->getRepository(ChatRoom::class)
                    ->createQueryBuilder('r')
                    ->where('(r.userOne = :user AND r.userTwo = :target) OR (r.userOne = :target AND r.userTwo = :user)')
                    ->setParameter('user', $user)
                    ->setParameter('target', $target)
                    ->getQuery()
                    ->getOneOrNullResult();

                if (!$existingRoom) {
                    $chatRoom = new ChatRoom();
                    $chatRoom->setUserOne($user);
                    $chatRoom->setUserTwo($target);
                    $chatRoom->setCreatedAt(new \DateTimeImmutable());

                    $entityManager->persist($chatRoom);
                }

                $notificationForUser = new Notification();
                $notificationForUser->setUser($user);
                $notificationForUser->setType(Notification::TYPE_MATCH_MUTUAL);
                $notificationForUser->setContent('Match mutuel 💘 Une nouvelle connexion vient de se créer.');
                $entityManager->persist($notificationForUser);

                $notificationForTarget = new Notification();
                $notificationForTarget->setUser($target);
                $notificationForTarget->setType(Notification::TYPE_MATCH_MUTUAL);
                $notificationForTarget->setContent('Match mutuel 💘 Une nouvelle connexion vient de se créer.');
                $entityManager->persist($notificationForTarget);

                $entityManager->persist($reverseCrush);

                $this->addFlash(
                    'success',
                    'Match mutuel 💘 - Compatibilité : ' . $compatibility . '% - Chat débloqué 🔓'
                );
            } else {
                $notification = new Notification();
                $notification->setUser($target);
                $notification->setType(Notification::TYPE_CRUSH_RECEIVED);
                $notification->setContent('Quelqu’un t’aime 💌');
                $entityManager->persist($notification);

                $this->addFlash('success', 'Crush envoyé 💌 - Compatibilité : ' . $compatibility . '%');
            }

            $entityManager->persist($crush);
            $entityManager->flush();

            return $this->redirectToRoute('app_crush_new');
        }

        return $this->render('crush/index.html.twig', [
            'form' => $form->createView(),
        ]);
    }
}