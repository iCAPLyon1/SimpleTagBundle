<?php

/**
*
* @author: Panagiotis TSAVDARIS <ptsavdar@gmail.com>
* @licence: GPL
*
*/

namespace ICAPLyon1\Bundle\SimpleTagBundle\Entity;

/**
* TaggableInterface
*/
interface TaggableInterface
{
    /**
* Get id must return a unique identifier.
*
* @return string | int
*/
    public function getId();
}