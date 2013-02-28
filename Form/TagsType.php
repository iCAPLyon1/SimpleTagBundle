<?php

namespace ICAPLyon1\Bundle\SimpleTagBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use ICAPLyon1\Bundle\SimpleTagBundle\Form\DataTransformer\TagsToTextTransformer;

class TagsType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->appendClientTransformer(new TagsToTextTransformer($options['manager']));
    }
    
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setRequired(array(
            'manager',
        ));

        $resolver->setAllowedTypes(array(
            'manager' => 'ICAPLyon1\Bundle\SimpleTagBundle\Service\Manager',
        ));
    }

    public function getParent()
    {
        return 'text';
    }
 
    public function getName()
    {
        return 'tags';
    }
}