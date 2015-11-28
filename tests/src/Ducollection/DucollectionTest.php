<?php

namespace Ehimen\Tests\Ducollection;

use Doctrine\Instantiator\Exception\InvalidArgumentException;
use Ehimen\Ducollection\Ducollection;
use Ehimen\Tests\Ducollection\Fixtures\Blog;
use Ehimen\Tests\Ducollection\Fixtures\User;


class DucollectionTest extends \PHPUnit_Framework_TestCase
{
    
    public function testInitialisable()
    {
        $this->assertInstanceOf(Ducollection::class, $this->getTestDucollection());
    }
    
    public function testThrowsIfClassNotExists()
    {
        $this->setExpectedException(\InvalidArgumentException::class);
        $this->getTestDucollection('foobar');
    }
    
    public function testAdd()
    {
        $collection = $this->getTestDucollection();
        $collection->add(new User());
        $this->assertCount(1, $collection);
    }
    
    public function testAddThrowsIfNotOfType()
    {
        $collection = $this->getTestDucollection();
        $this->setExpectedException(\InvalidArgumentException::class);
        $collection->add(new Blog());
    }
    
    
    private function getTestDucollection($className = User::class)
    {
        return new Ducollection($className);
    }
    
}