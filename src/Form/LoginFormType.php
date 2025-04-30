<?php

namespace App\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\NotBlank;

class LoginFormType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('email', TextType::class, [
                'label' => 'Email',
                'constraints' => [new NotBlank()],
            ])
            ->add('password', PasswordType::class, [
                'label' => 'Password',
                'constraints' => [new NotBlank()],
            ])
            ->add('_csrf_token', HiddenType::class, [
                'mapped' => false,
                'data' => $options['csrf_token'],
            ])
            ->add('login', SubmitType::class, ['label' => 'Log in']);
    }

    public function getBlockPrefix(): string
    {
        return 'login_form';
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'csrf_token' => null
        ]);
    }
}
