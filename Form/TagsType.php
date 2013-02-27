<?php

namespace ICAPLyon1\Bundle\SimpleTagBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use ICAPLyon1\Bundle\SimpleTagBundle\Form\DataTransformer\TagsToTextTransformer;

class TagsType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->appendClientTransformer(new TagsToTextTransformer());
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