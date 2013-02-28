<?php

/**
 * 
 * @author:  Panagiotis TSAVDARIS <ptsavdar@gmail.com>
 * @licence: GPL
 *
 */

namespace ICAPLyon1\Bundle\SimpleTagBundle\Twig;

class SimpleTagExtension extends \Twig_Extension
{
    protected $tagManager;

    public function __construct($tag_manager)
    {
        $this->tagManager = $tag_manager;
    }

    public function getTagManager()
    {
        return $this->tagManager;
    }

    public function getName()
    {
        return 'simple_tag';
    }

    public function getFunctions()
    {
        return array(
            'all_tags'     => new \Twig_Function_Method($this, 'get_all_tags'),
            'entity_tags' => new \Twig_Function_Method($this, 'get_tags_for_entity'),
        );
    }

    public function get_all_tags()
    {
        return $this->getTagManager()->getAllTags();
    }

    public function get_tags_for_entity($taggable)
    {
        return $this->getTagManager()->getTags($taggable);
    }
}