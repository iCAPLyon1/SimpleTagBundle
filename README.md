SimpleTagBundle
===============

Symfony2 bundle to easily manage tags with any entity.

Installation
===========

To install this bundle please follow the next steps:

First add the dependency in your `composer.json` file:
    
    ``` json
    "require": {
        ...
        "icap-lyon1/simple-tag-bundle": "dev-master"
    },
    ```

Then install the bundle with the command:

    php composer update

Enable the bundle in your application kernel:

    ``` php
    <?php
    // app/AppKernel.php

    public function registerBundles()
    {
        $bundles = array(
            // ...
            new ICAPLyon1\Bundle\SimpleTagBundle\ICAPLyon1SimpleTagBundle(),
        );
    }
    ```

Include the bundle configuration file in your app configuration file:
    
    ``` yaml
    // app/config/config.yml
    imports:
        // ...
        - { resource: @ICAPLyon1SimpleTagBundle/Resources/config/config.yml }
    ```

Now the Bundle is installed.


How to use
==========

In order to add tags to an entity, the entity has to implements TaggableInterface
example:
    
    ``` php
    <?php
    // Acme/Bundle/AcmeBundle/Entity/TaggableEntity.php

    namespace Acme\Bundle\AcmeBundle\Entity;

    use ICAPLyon1\Bundle\SimpleTagBundle\Entity\TaggableInterface;

    class TaggableEntity implements TaggableInterface
    { 
        // Your code here
    }
    ```

Then you need to add tags field in your entity's form 
example:
    
    ``` php
    <?php
    // Acme/Bundle/AcmeBundle/Form/TaggableEntityType.php

    namespace Acme\Bundle\AcmeBundle\Form;

    use Symfony\Component\Form\AbstractType;
    use Symfony\Component\Form\FormBuilderInterface;
    use Symfony\Component\OptionsResolver\OptionsResolverInterface;

    class TaggableEntityType extends AbstractType
    {
        public function buildForm(FormBuilderInterface $builder, array $options)
        {
            $builder
                // ...
                ->add('tags', 'tags', array(
                    'required'  => false,
                    'property_path' => false,
                ))
            ;
        }

        // The rest of your code here ...

    }
    ```

To associate, dissociate and get tags for your entity, in your entity's controller do the following:

To associate tags to your entity, in functions create and update of your entity add the following:
    
    ``` php
    <?php
    // Acme/Bundle/AcmeBundle/Controller/TaggableEntityController.php

    // ...
    public function createAction(Request $request)
    {
        $entity  = new TaggableEntity();
        $form = $this->createForm(new TaggableEntityType(), $entity);
        $form->bind($request);

        if ($form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($entity);
            $em->flush();

            //Get the tag names from form
            $data = $request->request->get($form->getName());
            $tagNames = $data['tags'];

            //Load tags if they exist already or create new tags
            $tags = $this->get("icaplyon1_simpletag.manager")->loadOrCreateTags($tagNames);
            
            //Associate tags with your entity
            $this->get("icaplyon1_simpletag.manager")->addTags($tags, $entity);

            return $this->redirect($this->generateUrl('taggableentity_show', array('id' => $entity->getId())));
        }

        return array(
            'entity' => $entity,
            'form'   => $form->createView(),
        );
    }

    // ...
    ```

if you want to dissociate a tag from your entity:
    
    ``` php
    $this->get("icaplyon1_simpletag.manager")->removeTag($tag, $entity);
    ```    

if you want to dissociate multiple tags from your entity:
    
    ``` php
    $this->get("icaplyon1_simpletag.manager")->removeTags($tags, $entity);
    ```

if you want to remove all tags from your entity (DO THIS WHEN YOU ARE DELETING YOUR ENTITY IN ORDER TO AVOID KEEPING RUBBISH IN YOUR DATABASE):
    
    ``` php
    $this->get("icaplyon1_simpletag.manager")->removeAllTags($entity);
    ```

example in method:
    
    ``` php
    // ...
    public function deleteAction(Request $request, $id)
    {
        $form = $this->createDeleteForm($id);
        $form->bind($request);

        if ($form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $entity = $em->getRepository('ICAPLyon1TestTagBundle:TaggableEntity')->find($id);

            if (!$entity) {
                throw $this->createNotFoundException('Unable to find TaggableEntity entity.');
            }
            
            //Remove all tags for entity
            $this->get("icaplyon1_simpletag.manager")->removeAllTags($entity);
            
            //Remove entity from database
            $em->remove($entity);
            $em->flush();
        }

        return $this->redirect($this->generateUrl('taggableentity'));
    }

    // ...
    ```

To get all tags for your entity:
    
    ``` php
    $this->get("icaplyon1_simpletag.manager")->getTags($entity);
    ```

