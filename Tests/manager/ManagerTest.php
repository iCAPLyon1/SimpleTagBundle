<?php

namespace ICAPLyon1\Bundle\SimpleTagBundle\Tests\Manager;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use ICAPLyon1\Bundle\SimpleTagBundle\Service\Manager;
use ICAPLyon1\Bundle\SimpleTagBundle\Tests\entity\TestA;

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

        $this->assertEquals(1, $tag->getId());
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
        $tags = array();
        $tags[] = $this->manager->loadOrCreateTag("music");
        $tags[] = $this->manager->loadOrCreateTag("video");
        $tags[] = $this->manager->loadOrCreateTag("text");

        $testA = new TestA();

        $this->manager->addTags($tags, $testA);

        $this->assertEquals(1,$testA->getId());
    }

    public function testRemoveTag()
    {
        $tags = array();
        $tags[] = $this->manager->loadTag("music");
        $tags[] = $this->manager->loadTag("video");
        $tags[] = $this->manager->loadTag("youtube");

        $testA = new TestA();

        $this->manager->removeTags($tags, $testA);

        $this->assertEquals(1,$testA->getId());
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