<?php

namespace ICAPLyon1\Bundle\SimpleTagBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="ICAPLyon1\Bundle\SimpleTagBundle\Repository\AssociatedTagRepository")
 * @ORM\Table(name="icap__associated_tag",
 *      uniqueConstraints={
 *          @ORM\UniqueConstraint(name="single_tag_hash", columns={"hash", "tag_id"})
 *      })
 */
class AssociatedTag
{
    /**
     * @ORM\Id
     * @ORM\Column(type="integer")
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $id;

    /**
     * @ORM\Column(type="string", length=64)
     */
    protected $hash;
    
    /**
     * @ORM\Column(type="string", length=255)
     */
    protected $taggableClass;

    /**
     * @ORM\Column(type="integer")
     */
    protected $taggableId;

    /**
     * @ORM\ManyToOne(targetEntity="ICAPLyon1\Bundle\SimpleTagBundle\Entity\Tag", inversedBy="associatedTags")
     */
    protected $tag;

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
     * Set hash
     *
     * @param string $hash
     * @return AssociatedTag
     */
    public function setHash($hash)
    {
        $this->hash = $hash;
    
        return $this;
    }

    /**
     * Get hash
     *
     * @return string 
     */
    public function getHash()
    {
        return $this->hash;
    }

    /**
     * Set taggableClass
     *
     * @param string $taggableClass
     * @return AssociatedTag
     */
    public function setTaggableClass($taggableClass)
    {
        $this->taggableClass = $taggableClass;
    
        return $this;
    }

    /**
     * Get taggableClass
     *
     * @return string 
     */
    public function getTaggableClass()
    {
        return $this->taggableClass;
    }

    /**
     * Set taggableId
     *
     * @param integer $taggableId
     * @return AssociatedTag
     */
    public function setTaggableId($taggableId)
    {
        $this->taggableId = $taggableId;
    
        return $this;
    }

    /**
     * Get taggableId
     *
     * @return integer 
     */
    public function getTaggableId()
    {
        return $this->taggableId;
    }

    /**
     * Set tag
     *
     * @param \ICAPLyon1\Bundle\SimpleTagBundle\Entity\Tag $tag
     * @return AssociatedTag
     */
    public function setTag(\ICAPLyon1\Bundle\SimpleTagBundle\Entity\Tag $tag = null)
    {
        $this->tag = $tag;
    
        return $this;
    }

    /**
     * Get tag
     *
     * @return \ICAPLyon1\Bundle\SimpleTagBundle\Tag 
     */
    public function getTag()
    {
        return $this->tag;
    }
}