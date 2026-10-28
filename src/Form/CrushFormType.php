<?php

namespace App\Form;

use App\Entity\User;
use App\Repository\UserRepository;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class CrushFormType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $currentUser = $options['current_user'];

        $builder->add('target', EntityType::class, [
            'class' => User::class,

            // On affiche le pseudo, jamais l'adresse mail : les emails restent privés.
            'choice_label' => fn (User $user) => $user->getProfile()?->getPseudo(),
            'label' => 'Choisis ton crush',
            'placeholder' => 'Sélectionne une personne',
            
            // Seules les personnes qui ont créé leur profil apparaissent dans la liste.
            'query_builder' => function (UserRepository $repository) use ($currentUser) {
                return $repository->createQueryBuilder('u')
                    ->innerJoin('u.profile', 'p')
                    ->addSelect('p')
                    ->where('u != :currentUser')
                    ->setParameter('currentUser', $currentUser)
                    ->orderBy('p.pseudo', 'ASC');
            },
        ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'current_user' => null,
        ]);
    }
}
