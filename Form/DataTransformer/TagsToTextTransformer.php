<?php
namespace ICAPLyon1\Bundle\SimpleTagBundle\Form\DataTransformer;

use Symfony\Component\Form\DataTransformerInterface;
use Symfony\Component\Form\Exception\TransformationFailedException;
use Doctrine\Common\Persistence\ObjectManager;
use ICAPLyon1\Bundle\SimpleTagBundle\Entity\Tag;

class TagsToTextTransformer implements DataTransformerInterface
{
    /**
     * Transforms objects (tags) to a string.
     *
     * @param  Tags|null $tags
     * @return string
     */
    public function transform($tags)
    {
        if (!$tags) {
            $tags = array(); // default value
        }
        
        $tagNames = array();
        foreach ($tags as $tag) {
            array_push($tagNames, $tag->getName());
        }

        return implode(', ', $tagNames);
    }

    /**
     * Transforms a string to an array of tags.
     *
     * @param  string $tagNames
     * @return array of strings (names for tags)
     */
    public function reverseTransform($tagNames)
    {
        if (!$tagNames) {
            $tagNames = ''; // default
        }

        $tagNamesArray = array_filter(array_map('trim', explode(',', $tagNames)));
        // 1. Split the string with commas
        // 2. Remove whitespaces around the tags
        // 3. Remove empty elements (like in "tag1,tag2, ,,tag3,tag4")

        $tags = array();
        foreach ($tagNamesArray as $tagName) {
            $tag = new Tag();
            $tag->setName(trim($tagName));
            array_push($tags, $tag);
        }

        return $tags;
    }

}