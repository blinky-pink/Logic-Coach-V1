<?php

namespace App\Form;

use App\Entity\DailyEntry;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class DailyEntryType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('sleepHours')
            ->add('energy')
            ->add('stress')
            ->add('motivation')
            ->add('mood')
            ->add('score')
            ->add('message')
            ->add('advice')
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => DailyEntry::class,
        ]);
    }
}
