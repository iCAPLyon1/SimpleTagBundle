SimpleTagBundle
===============

Symfony2 bundle to easily manage tags with any entity.

Installation
===========

To install this bundle please follow the next steps:

First add the dependency in your `composer.json` file:
    
```json
"require": {
    ...
    "icap-lyon1/simple-tag-bundle": "2.0.*"
},
```

Then install the bundle with the command:

```sh
php composer update
```

Enable the bundle in your application kernel:

```php
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
Then update your database schema:

```sh
php app/console doctrine:schema:update --force
```

Then install the bundle assets:

```sh
php app/console assets:install

// if you want to create a symlink:

php app/console assets:install --symlink
```

Finally include the bundle configuration file in your app configuration file:
    
```yaml
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
    
```php
<?php
// Acme/Bundle/AcmeBundle/Entity/TaggableEntity.php

namespace Acme\Bundle\AcmeBundle\Entity;

use ICAPLyon1\Bundle\SimpleTagBundle\Entity\TaggableInterface;

class TaggableEntity implements TaggableInterface
{ 
    // Your code here
}
```

Then when you wish to associate an entity with a tag, simply call the `icaplyon1_simpletag.manager` service to create a form and process it as explained below:
```php
// Instead of standard form creation
// $form = $this->createForm(new MyObjectType(), $myObject);

// Do this:
$form = $this->get('icaplyon1_simpletag.manager')->createForm(
    new TaggableEntityType(),
    $entity);
```

To save and associate the tags with your entity, call the processForm function like this:
```php
if ($form->isValid()) {
    $myObject = $this->get('icaplyon1_simpletag.manager')->processForm($form);

    return $this->redirect($this->generateUrl(...));
}
```

*The `processForm($form)` method will retrieve the input tags, add new (not already associated) tags and remove associated tags that are not included in the input list*

#Functions of `icaplyon1_simpletag.manager`
---------------------------------------------- 

### Associate tags
if you want to associate a tag with your entity:

```php
// ...
//Associate tags with your entity
$this->get("icaplyon1_simpletag.manager")->addTag($tag, $entity);
// ...
```

if you want to associate multiple tags with your entity:

```php
// ...
//Associate tags with your entity
$this->get("icaplyon1_simpletag.manager")->addTags($tags, $entity);
// ...
```

### Dissociate tags

if you want to dissociate a tag from your entity:
    
```php
$this->get("icaplyon1_simpletag.manager")->removeTag($tag, $entity);
```    

if you want to dissociate multiple tags from your entity:
    
```php
$this->get("icaplyon1_simpletag.manager")->removeTags($tags, $entity);
```

### Remove all tags from an entity

if you want to remove all tags from your entity (*DO THIS WHEN YOU ARE DELETING YOUR ENTITY IN ORDER TO AVOID KEEPING RUBBISH IN YOUR DATABASE*):
    
```php
$this->get("icaplyon1_simpletag.manager")->removeAllTags($entity);
```

example in method:
    
```php
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
## Get tags

### In your manager (php)

To get all tags for your entity:
    
```php
$this->get("icaplyon1_simpletag.manager")->getTags($entity);
```

### In a twig template

To get the tags associated to an object a twig extension has been created, use it as follows:

```twig
{{ entity_tags(entity) }}
```

## Get all stored tags

You can get all stored tags to use them for example for autocomplete

### Using php
```php
$this->get("icaplyon1_simpletag.manager")->getAllTags();
```
### Using twig
```twig
{{ all_tags() }}
```