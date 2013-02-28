<?php

/**
 * 
 * @author:  Panagiotis TSAVDARIS <ptsavdar@gmail.com>
 * @licence: GPL
 *
 */

namespace ICAPLyon1\Bundle\SimpleTagBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use ICAPLyon1\Bundle\SimpleTagBundle\Entity\Tag;
use ICAPLyon1\Bundle\SimpleTagBundle\Entity\AssociatedTag;

/**
 * TaggableType
 */
class TaggableType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $reflection = new \ReflectionClass($options['taggable']);
        $label = $reflection->getShortName();


        $builder
            ->add('taggable', $options['taggableType'], array(
                'property_path' => false,
                'label'         => $label,
                'data'          => $options['taggable'],
            ))
        ;

        $manager = $options['manager'];
        $tags = $manager->getTags($options['taggable']);
        $builder
            ->add('tags', 'tags', array(
                'property_path'   => false,
                'required'        => false,
                'data'            => $tags,
                'manager'         => $manager,
            ))
        ;
    }

    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'cascade_validation'  => true,
            'taggableType' => null,
            'taggable'     => null,

        ));

        $resolver->setRequired(array(
            'taggableType',
            'taggable',
            'manager',
        ));

        $resolver->setAllowedTypes(array(
            'taggableType' => 'Symfony\Component\Form\AbstractType',
            'taggable'     => 'ICAPLyon1\Bundle\SimpleTagBundle\Entity\TaggableInterface',
            'manager'      => 'ICAPLyon1\Bundle\SimpleTagBundle\Service\Manager',
        ));
    }

    public function getName()
    {
        return 'icaplyon1simpletagbundle_taggabletype';
    }
}