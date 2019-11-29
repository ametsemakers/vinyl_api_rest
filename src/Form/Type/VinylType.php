<?php

namespace App\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class VinylType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add('artist');
        $builder->add('title_album');
        $builder->add('label');
        $builder->add('country');
        $builder->add('cat_nb');
        $builder->add('year_original');
        $builder->add('year_edition');
        $builder->add('songs', CollectionType::class, [
            'entry_type' => SongType::class,
            'allow_add' =>true,
            'error_bubbling' => false,
        ]);
        $builder->add('image', ImageType::class);
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => 'App\Entity\Vinyl',
            'csrf_protection' => false
        ]);
    }
}