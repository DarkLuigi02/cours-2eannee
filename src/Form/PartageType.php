<?php

namespace App\Form;

use App\Entity\Fichier;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Config\TwigConfig;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;

class PartageType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
        ->add('utilisateur', EntityType:: class, array('class'=>'App\Entity\Utilisateur', 'choice_label'=>function ($utilisateur){
            return $utilisateur->getPrenom().' '.$utilisateur->getNom();
        }))
        ->add('partager', SubmitType::class)
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Fichier::class,
        ]);
    }
}
