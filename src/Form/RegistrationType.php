<?php

namespace App\Form;

use App\Entity\User;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;

use Symfony\Component\Validator\Constraints as Assert;

class RegistrationType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {

        $builder
            ->add('username', TextType::class, [
                'help' => "The name you use to log in with and is displayed to others.",
                'required' => true
            ])
            ->add('email', EmailType::class, [
                'help' => "Used for site related contact. We'll email you a random password for your first login.",
                'required' => true
            ])
            ->add('full_name', TextType::class, [
                'help' => "Your real name will only be visible to organizers of competitions. This must match your NI registered name if submitting for recertification points.",
                'required' => true
            ])
            ->add('company', TextType::class, [
                'help' => "Optional. May be used by your employer for company tournaments or for extra tournament groupings.",
                'required' => false
            ])
            ->add('country', TextType::class, [
                'help' => "Optional. May be used for extra tournament groupings if supplied. Not displayed on the site.",
                'required' => false
            ])
            ->add('state', TextType::class, [
                'help' => "Optional. May be used for extra tournament groupings if supplied. Not displayed on the site.",
                'required' => false
            ])
            ->add('ni_email', EmailType::class, [
                'help' => "Optional. Used to submit recertification point validation to NI if supplied. Not displayed on the site.",
                'required' => false,
                'label' => "NI Account Email"
            ])
            ->add('clad', CheckboxType::class, [
                'help' => "Optional. Specifies if you have a CLAD certification and may be used for extra tournament groupings if checked.",
                'required' => false,
                'label' => "CLAD Certified"
            ])
            ->add('cld', CheckboxType::class, [
                'help' => "Optional. Specifies if you have a CLD certification and may be used for extra tournament groupings if checked.",
                'required' => false,
                'label' => "CLD Certified"
            ])
            ->add('cla', CheckboxType::class, [
                'help' => "Optional. Specifies if you have a CLA certification and may be used for extra tournament groupings if checked.",
                'required' => false,
                'label' => "CLA Certified"
            ])
            ->add('cled', CheckboxType::class, [
                'help' => "Optional. Specifies if you have a CLED certification and may be used for extra tournament groupings if checked.",
                'required' => false,
                'label' => "CLED Certified"
            ])
            ->add('ctd', CheckboxType::class, [
                'help' => "Optional. Specifies if you have a CTD certification and may be used for extra tournament groupings if checked.",
                'required' => false,
                'label' => "CTD Certified"
            ])
            ->add('cta', CheckboxType::class, [
                'help' => "Optional. Specifies if you have a CTA certification and may be used for extra tournament groupings if checked.",
                'required' => false,
                'label' => "CTA Certified"
            ])
            ->add('champion', CheckboxType::class, [
                'help' => "Optional. May be used for extra tournament groupings if checked and validated.",
                'required' => false,
                'label' => "LabVIEW Champion"
            ])
            ->add('ni_employee', CheckboxType::class, [
                'help' => "Optional. May be used for extra tournament groupings if checked and validated.",
                'required' => false,
                'label' => "NI Employee"
            ])
            ->add('partner', CheckboxType::class, [
                'help' => "Optional. May be used for extra tournament groupings if checked and validated.",
                'required' => false,
                'label' => "NI Partner"
            ])
        ;
        
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => User::class,
        ]);
    }
}
