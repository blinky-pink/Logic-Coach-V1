<?php

namespace App\Form;

use App\Entity\DailyEntry;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\Range;

class DailyEntryType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('sleepHours', NumberType::class, [
                'constraints' => [
                    new Range(min: 0, max: 24),
                ],
            ])
            ->add('energy', IntegerType::class, [
                'constraints' => [
                    new Range(min: 0, max: 10),
                ],
            ])
            ->add('stress', IntegerType::class, [
                'constraints' => [
                    new Range(min: 0, max: 10),
                ],
            ])
            ->add('motivation', IntegerType::class, [
                'constraints' => [
                    new Range(min: 0, max: 10),
                ],
            ])
            ->add('mood', IntegerType::class, [
                'constraints' => [
                    new Range(min: 0, max: 10),
                ],
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => DailyEntry::class,
        ]);
    }
}
