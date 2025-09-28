<?php

declare(strict_types=1);

namespace App\Front\Form;

use App\Contact\Entity\Contact;
use Karser\Recaptcha3Bundle\Form\Recaptcha3Type;
use Karser\Recaptcha3Bundle\Validator\Constraints\Recaptcha3;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ContactForm extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('email', EmailType::class, [
                'label' => 'Email',
                'required' => true,
                'help' => 'Заполните Email',
            ])
            ->add('name', TextType::class, [
                'label' => 'Имя',
                'required' => false,
                'help' => 'Введи имя',
            ])
            ->add('phone', TextType::class, [
                'label' => 'Телефон',
                'required' => false,
                'help' => 'Введи телефон',
            ])
            ->add('message', TextareaType::class, [
                'label' => 'Сообщение',
                'required' => true,
                'help' => 'Заполните основой текст',
            ])
            ->add('captcha', Recaptcha3Type::class, [
                'constraints' => new Recaptcha3(),
                'action_name' => 'contact',
                'locale' => 'ru',
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Contact::class,
            'csrf_protection' => true,
        ]);
    }
}
