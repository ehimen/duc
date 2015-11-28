<?php

namespace Ehimen\Tests\DuCollection;

use Ehimen\Ducollection\DuCollection;
use Ehimen\Tests\DuCollection\Fixtures\Blog;
use Ehimen\Tests\DuCollection\Fixtures\User;


class DuCollectionTest extends \PHPUnit_Framework_TestCase
{
    
    public function testInitialisable()
    {
        $this->assertInstanceOf(DuCollection::class, $this->getTestDuCollection());
    }
    
    public function testThrowsIfClassNotExists()
    {
        $this->setExpectedException(\InvalidArgumentException::class);
        $this->getTestDuCollection('foobar');
    }
    
    public function testAdd()
    {
        $collection = $this->getTestDuCollection();
        $collection->add(new User());
        $this->assertCount(1, $collection);
    }
    
    public function testAddThrowsIfNotOfType()
    {
        $collection = $this->getTestDuCollection();
        $this->setExpectedException(\InvalidArgumentException::class);
        $collection->add(new Blog());
    }
    
    
    private function getTestDuCollection($className = User::class)
    {
        return new DuCollection($className);
    }
    
}