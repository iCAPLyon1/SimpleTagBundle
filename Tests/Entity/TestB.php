<?php
/**
 * Test entity to test manager functionnalities
 * 
 */
namespace ICAPLyon1\Bundle\SimpleTagBundle\Tests\Entity;

use ICAPLyon1\Bundle\SimpleTagBundle\Entity\TaggableInterface;

class TestB implements TaggableInterface
{
    public function __construct()
    {

    }

    public function getId()
    {
        return 1;
    }
}