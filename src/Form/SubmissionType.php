<?php

namespace App\Form;

use App\Entity\Submission;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Validator\Constraints\File;

class SubmissionType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {

        $builder
            ->add('file', FileType::class, [
                'help' => 'The zip created by the build spec in your project.',
                'label' => 'Submission Zip',
                'mapped' => false,
                'required' => true,
                'constraints' => [
                    new File([
                        'mimeTypes' => ['application/zip'],
                        'mimeTypesMessage' => 'Please select a project build Zip file'
                    ])
                    ],
            ])
            ->add('recert_points', CheckboxType::class, [
                'help' => 'Check if you\'d like to earn recertification points. Your submission must be able to be run and your NI account email must be set on your account.',
                'required' => false,
                'label' => 'Recertification Points',
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Submission::class,
        ]);
    }
}