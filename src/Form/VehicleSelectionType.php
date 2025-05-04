<?php
namespace App\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class VehicleSelectionType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('vehicle', ChoiceType::class, [
                'choices' => $options['vehicles'], // Pass available vehicles as choices
                'choice_label' => false,
                'choice_value' => 'id', // Use vehicle ID as the value
                'expanded' => true, // Set to true for radio buttons
                'multiple' => false, // Single selection
            ])
            ->add('submit', SubmitType::class, [
                'label' => 'Confirm Vehicle',
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'vehicles' => [], // Default empty array for available vehicles
        ]);
    }
}