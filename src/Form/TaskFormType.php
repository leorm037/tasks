<?php

namespace App\Form;

use App\Entity\Task;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\DateTimeType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class TaskFormType extends AbstractType
{

    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
                ->add('name', null, [
                    'label' => 'Name'
                ])
                ->add('description', TextareaType::class, [
                    'label' => 'Description',
                    'required' => false,
                ])
                ->add('done', ChoiceType::class, [
                    'choices' => ['option.no' => false, 'option.yes' => true],
                    'label' => 'Done'
                ])
                ->add('createdAt', DateTimeType::class, [
                    'label' => 'CreatedAt',
                    'required' => false,
                    'disabled' => true,
                    'widget' => 'single_text',
                    'html5' => false,
                    'format' => 'dd/MM/y H:mm:ss',
                    'model_timezone' => 'America/Sao_Paulo',
                ])
                ->add('updatedAt', DateTimeType::class, [
                    'label' => 'UpdatedAt',
                    'required' => false,
                    'disabled' => true,
                    'widget' => 'single_text',
                    'html5' => false,
                    'format' => 'dd/MM/y HH:mm:ss',
                    'model_timezone' => 'America/Sao_Paulo',
                ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Task::class,
            'translation_domain' => 'label',
        ]);
    }
}
