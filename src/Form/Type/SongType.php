<?php

namespace App\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class SongType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add('title_song');
        $builder->add('artist');
        $builder->add('alternate_info');
        $builder->add('featuring');
        $builder->add('title_album');
        $builder->add('side');
        $builder->add('position');
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => 'App\Entity\Song',
            'csrf_protection' => false
        ]);
    }
}