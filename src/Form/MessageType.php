<?php

namespace App\Form;

use App\Entity\Message;
use App\Entity\User;
use Doctrine\ORM\QueryBuilder;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class MessageType extends AbstractType
{
    public function __construct(
        private readonly Security $security
    ) {
    }

    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        /** @var User $currentUser */
        $currentUser = $this->security->getUser();

        $builder
            ->add('receiver', EntityType::class, [
                'class' => User::class,
                'choice_label' => 'pseudo',
                'label' => 'Destinataire',
                'query_builder' => function (\Doctrine\ORM\EntityRepository $repository) use ($currentUser): QueryBuilder {
                    return $repository->createQueryBuilder('u')
                        ->where('u.id != :currentUserId')
                        ->setParameter('currentUserId', $currentUser->getId())
                        ->orderBy('u.pseudo', 'ASC');
                },
            ])
            ->add('message', null, [
                'label' => 'Message',
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Message::class,
        ]);
    }
}