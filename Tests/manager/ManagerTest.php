<?php

namespace ICAPLyon1\Bundle\SimpleTagBundle\Tests\Manager;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use ICAPLyon1\Bundle\SimpleTagBundle\Service\Manager;
use ICAPLyon1\Bundle\SimpleTagBundle\Tests\entity\TestA;
use ICAPLyon1\Bundle\SimpleTagBundle\Tests\entity\TestB;

class ManagerTest extends WebTestCase
{
    /**
     * @var \Doctrine\ORM\EntityManager
     */
    private $em;

    /**
     * @var \ICAPLyon1\Bundle\SimpleTagBundle\Service\Manager
     */
    private $manager;

    /**
     * {@inheritDoc}
     */
    public function setUp()
    {
        static::$kernel = static::createKernel();
        static::$kernel->boot();
        $this->em = static::$kernel->getContainer()
            ->get('doctrine')
            ->getManager()
        ;

        $this->manager = new Manager($this->em);
    }

    public function testCreateTag()
    {
        $tag = $this->manager->createTag("new");
        if($tag) $this->assertEquals(1, $tag->getId());
    }

    public function testLoadTag()
    {
        $tag = $this->manager->loadTag("video");

        $this->assertEquals(2, $tag->getId());
    }

    public function testLoadOrCreateTag()
    {
        $tag = $this->manager->loadOrCreateTag("music");

        $this->assertEquals(5, $tag->getId());

        $tag = $this->manager->loadOrCreateTag("youtube");
        $tag = $this->manager->loadOrCreateTag("video");

        $this->assertEquals(2, $tag->getId());
    }

    public function testAddTag()
    {
        $testA = new TestA();
        $tag = $this->manager->loadOrCreateTag("text");
        $this->manager->addTag($tag, $testA);

        $this->assertEquals(1, $testA->getId());
    }

    public function testAddTags()
    {
        $testB = new TestB();
        $tags = array();
        $tags[] = $this->manager->loadOrCreateTag("music");
        $tags[] = $this->manager->loadOrCreateTag("video");
        $tags[] = $this->manager->loadOrCreateTag("text");
        $this->manager->addTags($tags, $testB);

        $this->assertEquals(1,$testB->getId());
    }

    public function testRemoveTag()
    {
        $testA = new TestA();
        $tag = $this->manager->loadTag("youtube");
        $this->manager->removeTag($tag, $testA);
        $tag = $this->manager->loadTag("text");
        $this->manager->removeTag($tag, $testA);

        $this->assertEquals(1, $testA->getId());
    }

    public function testRemoveTags()
    {
        $tags = array();
        $tags[] = $this->manager->loadTag("music");
        $tags[] = $this->manager->loadTag("video");
        $tags[] = $this->manager->loadTag("youtube");
        $tags[] = $this->manager->loadTag("new");
        $testB = new TestB();
        $this->manager->removeTags($tags, $testB);

        $this->assertEquals(1, $testB->getId());
    }

    /**
     * {@inheritDoc}
     */
    protected function tearDown()
    {
        parent::tearDown();
        $this->em->close();
    }
}