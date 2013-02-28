<?php

namespace ICAPLyon1\Bundle\SimpleTagBundle\Service;

use Doctrine\ORM\EntityManager;
use Symfony\Component\Form\FormFactory;
use ICAPLyon1\Bundle\SimpleTagBundle\Entity\Tag;
use ICAPLyon1\Bundle\SimpleTagBundle\Entity\AssociatedTag;
use ICAPLyon1\Bundle\SimpleTagBundle\Entity\TaggableInterface;
use ICAPLyon1\Bundle\SimpleTagBundle\Form\TaggableType;
use ICAPLyon1\Bundle\SimpleTagBundle\Exception\AlreadyAssociatedTagException;
use ICAPLyon1\Bundle\SimpleTagBundle\Exception\UnassociatedTagException;

class Manager
{
    protected $em;
    protected $formFactory;

    /**
     * @return ICAPLyon1\Bundle\SimpleTagBundle\Entity\Tag
     */
    protected function getTagRepository()
    {
        return $this->getEntityManager()->getRepository('ICAPLyon1SimpleTagBundle:Tag');
    }

    /**
     * @return ICAPLyon1\Bundle\SimpleTagBundle\Entity\AssociatedTag
     */
    protected function getAssociatedTagRepository()
    {
        return $this->getEntityManager()->getRepository('ICAPLyon1SimpleTagBundle:AssociatedTag');
    }

    /**
     * Gets taggable objects using associated tags
     * @param array of ICAPLyon1\Bundle\SimpleTagBundle\Entity\AssociatedTag $associatedTags
     * @return array
     */
    protected function getTaggablesFromAssociatedTags($associatedTags)
    {
        $taggables = array();
        foreach ($associatedTags as $associatedTag) {
            $taggables[] = $this->getEntityManager()
                ->getRepository($associatedTag->getTaggableClass())
                ->findOneBy($associatedTag->getTaggableId());
        }

        return $taggables;
    }

    /**
     * Filter a given tag list to keep new tags
     *
     * @param array $tags
     * @param  ICAPLyon1\Bundle\SimpleTagBundle\Entity\TaggableInterface $taggable
     */
    protected function filterNewTags($tags, TaggableInterface $taggable)
    {
        $oldTags = $this->getTags($taggable);

        return array('add' => array_diff($tags, $oldTags), 'remove' => array_diff($oldTags, $tags));
    }

    /**
     * Constructor
     *
     * @param Doctrine\ORM\EntityManager
     */
    public function __construct(EntityManager $em, FormFactory $form_factory)
    {
        $this->em = $em;
        $this->formFactory = $form_factory;
    }

    /**
     * @return EntityManager
     */
    public function getEntityManager()
    {
        return $this->em;
    }

    /**
     * @return FormFactory
     */
    public function getFormFactory()
    {
        return $this->formFactory;
    }

    /**
     * Is a proxy class
     *
     * @param ReflectionClass $reflection
     * @return boolean
     */
    public static function isProxyClass(\ReflectionClass $reflection)
    {
        return in_array('Doctrine\ORM\Proxy\Proxy', array_keys($reflection->getInterfaces()));
    }

    /**
     * Get Class for a given object which mush implement TaggableInterface
     *
     * @param ICAPLyon1\Bundle\SimpleTagBundle\Entity\TaggableInterface $taggable
     * @return string Taggable's class
     */
    public function getTaggableClass(TaggableInterface $taggable)
    {
        $reflection = new \ReflectionClass($taggable);

        if (self::isProxyClass($reflection) && $reflection->getParentClass()) {
            $reflection = $reflection->getParentClass();
        }

        return $reflection->getName();
    }

    /**
     * Get Hash for a given object which mush implement TaggableInterface
     *
     * @param ICAPLyon1\Bundle\SimpleTagBundle\Entity\TaggableInterface $taggable
     * @return string The generated hash
     */
    public function getHash(TaggableInterface $taggable)
    {
        $taggableClass = $this->getTaggableClass($taggable);

        $raw = sprintf('%s_%s',
            $taggableClass,
            $taggable->getId()
        );

        return md5($raw);
    }

    /**
     * Create new Tag given its name
     *
     * @param String $name
     * @return Tag the generated Tag
     */
    public function createTag($name)
    {
        $tag = $this->getTagRepository()->findOneByName($name);
        if(!$tag)
        {
            $tag = new Tag();
            $tag->setName($name);
            $this->getEntityManager()->persist($tag);
            $this->getEntityManager()->flush();

            return $tag;
        }
        else{
            
        }
    }

    /**
     * Load a tag following to its name
     *
     * @param string $name
     * @return ICAPLyon1\Bundle\SimpleTagBundle\Entity\Tag
     */
    public function loadTag($name)
    {
        return $this->getTagRepository()->findOneByName($name);
    }

    /**
     * Load a tag following to its id
     *
     * @param int $id
     * @return ICAPLyon1\Bundle\SimpleTagBundle\Entity\Tag
     */
    public function loadTagById($id)
    {
        return $this->getTagRepository()->findOneById($id);
    }

    /**
     * Load or Create tag following to a given name
     *
     * @param string $name
     * @return ICAPLyon1\Bundle\SimpleTagBundle\Entity\Tag
     */
    public function loadOrCreateTag($name)
    {
        $tag = $this->loadTag($name);
        if (!$tag) {
            $tag = $this->createTag($name);
        }

        return $tag;        
    }

    /**
     * Load or Create tag following to a given string or list of names
     *
     * @param string or array $tagNames
     * @return array tags
     */
    public function loadOrCreateTags($tagNames)
    {
        $tagNames = (is_array($tagNames)) ? $tagNames : array_filter(array_map('trim', explode(',', $tagNames)));

        $tags = array();
        foreach ($tagNames as $name) {
            if ($name) {
                $tags[] = $this->loadOrCreateTag($name);
            }
        }
        
        return $tags;
    }

    /**
     * Check if a tag is already associated with the given taggable object
     *
     * @param  ICAPLyon1\Bundle\SimpleTagBundle\Entity\Tag $tag
     * @param  ICAPLyon1\Bundle\SimpleTagBundle\Entity\TaggableInterface $taggable
     * @return boolean
     */
    public function hasTag($tag, TaggableInterface $taggable)
    {
        //Get taggable data
        $hash = $this->getHash($taggable);
        //Get tag if exists
        $associatedTag = $this->getAssociatedTagRepository()->findOneBy(array(
          'hash' => $hash,
          'tag' => $tag,  
        ));

        return ($associatedTag) ? true : false;
    }

    /**
     * Associates a tag with a taggable object
     *
     * @param  ICAPLyon1\Bundle\SimpleTagBundle\Entity\Tag $tag
     * @param  ICAPLyon1\Bundle\SimpleTagBundle\Entity\TaggableInterface $taggable
     * @throw ICAPLyon1\Bundle\SimpleTagBundle\Exception\AlreadyAssociatedTagException
     * @return boolean
     */
    public function addTag($tag, TaggableInterface $taggable)
    {
        if ($this->hasTag($tag, $taggable)) {
            throw new AlreadyAssociatedTagException("Tag is already associated to object");
        } else {
            //Get taggable data
            $taggableClass = $this->getTaggableClass($taggable);
            $hash = $this->getHash($taggable);
            $associatedTag = new AssociatedTag();
            $associatedTag->setTag($tag);
            $associatedTag->setHash($hash);
            $associatedTag->setTaggableClass($taggableClass);
            $associatedTag->setTaggableId($taggable->getId());
            $this->getEntityManager()->persist($associatedTag);
            $this->getEntityManager()->flush();
        }
        return true;
    }

    /**
     * To associate a tag list with a taggable object
     *
     * @param  array $tags
     * @param  ICAPLyon1\Bundle\SimpleTagBundle\Entity\TaggableInterface $taggable
     * @return boolean
     */
    public function addTags($tags, TaggableInterface $taggable)
    {
        $tagsArray = (is_array($tags)) ? $tags : array($tags);
        //Get taggable data
        $hash = $this->getHash($taggable);
        $taggableClass = $this->getTaggableClass($taggable);
        $taggableId = $taggable->getId();
        //Filter tags, keep only new tags
        $addRemoveTagsArray = $this->filterNewTags($tags, $taggable);
        foreach ($addRemoveTagsArray['add'] as $tag) {
            $associatedTag = new AssociatedTag();
            $associatedTag->setTag($tag);
            $associatedTag->setHash($hash);
            $associatedTag->setTaggableClass($taggableClass);
            $associatedTag->setTaggableId($taggableId);
            $this->getEntityManager()->persist($associatedTag);
        }
        $this->getEntityManager()->flush();

        return true;
    }

    /**
     * To remove an associated tag with a taggable object
     *
     * @param  ICAPLyon1\Bundle\SimpleTagBundle\Entity\Tag $tag
     * @param  ICAPLyon1\Bundle\SimpleTagBundle\Entity\TaggableInterface $taggable
     * @throw ICAPLyon1\Bundle\SimpleTagBundle\Exception\UnassociatedTagException
     * @return boolean
     */
    public function removeTag($tag, TaggableInterface $taggable)
    {
        //Get taggable data
        $hash = $this->getHash($taggable);
        //Get tag if exists
        $associatedTag = $this->getAssociatedTagRepository()->findOneBy(array(
          'hash' => $hash,
          'tag' => $tag,  
        ));
        if ($associatedTag) {
            $this->getEntityManager()->remove($associatedTag);
            $this->getEntityManager()->flush();
        } else {
            throw new UnassociatedTagException("The tag is not associated!");
        }

        return true;
    }

    /**
     * To remove an associated tag list with a taggable object
     *
     * @param  array $tags
     * @param  ICAPLyon1\Bundle\SimpleTagBundle\Entity\TaggableInterface $taggable
     * @return boolean
     */
    public function removeTags($tags, TaggableInterface $taggable)
    {
        //Get taggable data
        $hash = $this->getHash($taggable);
        foreach ($tags as $tag) {
            $associatedTag = $this->getAssociatedTagRepository()->findOneBy(array(
              'hash' => $hash,
              'tag' => $tag,  
            ));
            if ($associatedTag) {
                $this->getEntityManager()->remove($associatedTag);
            } 
        }
        $this->getEntityManager()->flush();

        return true;
    }

    /**
     * To associate a tag list with a taggable object 
     * and dissociate all tags that are associated to entity and are not in the given list of tags
     *
     * @param  array $tags
     * @param  ICAPLyon1\Bundle\SimpleTagBundle\Entity\TaggableInterface $taggable
     * @return boolean
     */
    public function addRemoveTags($tags, TaggableInterface $taggable)
    {
        $tagsArray = (is_array($tags)) ? $tags : array($tags);
        //Get taggable data
        $hash = $this->getHash($taggable);
        $taggableClass = $this->getTaggableClass($taggable);
        $taggableId = $taggable->getId();
        //Filter tags, keep only new tags
        $addRemoveTagsArray = $this->filterNewTags($tags, $taggable);
        foreach ($addRemoveTagsArray['add'] as $tag) {
            $associatedTag = new AssociatedTag();
            $associatedTag->setTag($tag);
            $associatedTag->setHash($hash);
            $associatedTag->setTaggableClass($taggableClass);
            $associatedTag->setTaggableId($taggableId);
            $this->getEntityManager()->persist($associatedTag);
        }
        $this->getEntityManager()->flush();

        $this->removeTags($addRemoveTagsArray['remove'], $taggable);

        return true;
    }

    /**
     * Retrieve taggable objects following to given tags
     *
     * @param array $tags
     * @return DoctrineCollection
     */
    public function findAll($tags, $operator)
    {
        $tagIds = array();
        foreach ($tags as $tag) {
            $tagIds[] = $tag->getId();
        }
        $associatedTags = $this->getAssociatedTagRepository()->getAssociatedTags($tagIds);

        return $this->getTaggablesFromAssociatedTags($associatedTags);
    }

    /**
     * Retrieve taggable objects instance of given className following to given tags
     *
     * @param array $tags
     * @return array
     */
    public function findByClassName($tags, $className)
    {
        $tagIds = array();
        foreach ($tags as $tag) {
            $tagIds[] = $tag->getId();
        }
        $associatedTags = $this->getAssociatedTagRepository()->getAssociatedTagsByClass($tagIds, $className);

        return $this->getTaggablesFromAssociatedTags($associatedTags);
    }

    /**
     * Retrieve tag associated to the given taggable object
     *
     * @param  ICAPLyon1\Bundle\SimpleTagBundle\Entity\TaggableInterface $taggable
     * @return array
     */
    public function getTags(TaggableInterface $taggable)
    {
        //Get hash for taggable
        $hash = $this->getHash($taggable);
        $associatedTags = $this->getAssociatedTagRepository()->findBy(array(
              'hash' => $hash,
        ));
        $tags = array();
        foreach ($associatedTags as $associatedTag) {
            $tags[] = $associatedTag->getTag();
        }

        return $tags;
    }

    /**
     * Retrieve all stored tags
     *
     * @return array
     */
    public function getAllTags()
    {
        return $this->getTagRepository()->findAll();
    }

    /**
     * Removes tag associated to the given taggable object
     *
     * @param  ICAPLyon1\Bundle\SimpleTagBundle\Entity\TaggableInterface $taggable
     * @return boolean
     */
    public function removeAllTags(TaggableInterface $taggable)
    {
        //Get hash for taggable
        $hash = $this->getHash($taggable);
        $associatedTags = $this->getAssociatedTagRepository()->findBy(array(
              'hash' => $hash,
        ));
        foreach ($associatedTags as $associatedTag) {
            $this->getEntityManager()->remove($associatedTag);
        }
        $this->getEntityManager()->flush();

        return true;
    }

    /**
     * create a form based on a given Type/TaggableInterface and attach media input fields
     *
     * @param FormType $form
     * @param array $options
     * @return FormType
     */
    public function createForm($type, TaggableInterface &$taggable)
    {
        return $this->getFormFactory()->create(
            new TaggableType(),
            null,
            array(
                'taggableType' => $type,
                'taggable'     => $taggable,
                'manager'      => $this,
            )
        );
    }

    /**
     * process a form
     *
     * @param Form $form
     * @return void
     */
    public function processForm($form)
    {
        $taggable = $form->get('taggable')->getData();
        $tags = $form->get('tags')->getData();

        $this->getEntityManager()->persist($taggable);
        $this->getEntityManager()->flush();
        $this->addRemoveTags($tags, $taggable);

        return $taggable;
    }

}
