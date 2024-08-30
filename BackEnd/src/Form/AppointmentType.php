<?php

namespace App\Form;

use App\Entity\Appointment;
use App\Entity\Patient;
use App\Entity\User;
use App\Entity\Test;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\DateTimeType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;


class AppointmentType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
        ->add('id', HiddenType::class,[
            'required' => false,
            'mapped' => false,
        ])
        ->add('DateTimeOfAppointment', DateTimeType::class, [
            'widget' => 'single_text',
        ])
        ->add('TypeOfTest', EntityType::class, [
            'class' => Test::class,
            'choice_label' => 'Name', // Assuming Test entity has a Name field
        ])
        ->add('Confirmation', ChoiceType::class, [
            'required' => true,
            'choices'  => [
                'Pending' => 'Pending',
                'Done' => 'Done',
                'Canceled' => 'Canceled',
            ],
                'multiple' => false,
                'label' => 'Confirmation',
                'attr' => ['class' => 'form-control'],
        ])
        ->add('doctor', EntityType::class, [
            'class' => User::class,
            'choice_label' => 'FirstName', // Assuming User entity has a name field
        ])
        ->add('patient', EntityType::class, [
            'class' => Patient::class,
            'choice_label' => 'id_number', // Assuming Patient entity has a FirstName field
        ]);
}

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Appointment::class,
        ]);
    }
}
