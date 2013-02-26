<?php

namespace ICAPLyon1\Bundle\SimpleTagBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="ICAPLyon1\Bundle\SimpleTagBundle\Repository\TagRepository")
 * @ORM\Table(name="icap__tag")
 */
class Tag
{
    /**
     * @ORM\Id
     * @ORM\Column(type="integer")
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $id;

    /**
     * @ORM\Column(type="string", length=255, unique=true)
     */
    protected $name;

    /**
     * @ORM\OneToMany(targetEntity="ICAPLyon1\Bundle\SimpleTagBundle\Entity\AssociatedTag", mappedBy="tag", orphanRemoval=true)
     */
    protected $associatedTags;

    /**
     * Constructor
     */
    public function __construct()
    {
        $this->associatedTags = new \Doctrine\Common\Collections\ArrayCollection();
    }

    /**
     * to string method
     *
     * @return String name
     */
    public function __toString()
    {
        return $this->name;
    }

    /**
     * Get id
     *
     * @return integer 
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set name
     *
     * @param string $name
     * @return Tag
     */
    public function setName($name)
    {
        $this->name = $name;
    
        return $this;
    }

    /**
     * Get name
     *
     * @return string 
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * Add associatedTags
     *
     * @param \ICAPLyon1\Bundle\SimpleTagBundle\Entity\AssociatedTag $associatedTags
     * @return Tag
     */
    public function addAssociatedTag(\ICAPLyon1\Bundle\SimpleTagBundle\Entity\AssociatedTag $associatedTags)
    {
        $this->associatedTags[] = $associatedTags;
    
        return $this;
    }

    /**
     * Remove associatedTags
     *
     * @param \ICAPLyon1\Bundle\SimpleTagBundle\Entity\AssociatedTag $associatedTags
     */
    public function removeAssociatedTag(\ICAPLyon1\Bundle\SimpleTagBundle\Entity\AssociatedTag $associatedTags)
    {
        $this->associatedTags->removeElement($associatedTags);
    }

    /**
     * Get associatedTags
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getAssociatedTags()
    {
        return $this->associatedTags;
    }
}