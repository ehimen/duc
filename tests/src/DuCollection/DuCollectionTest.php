<?php

namespace Ehimen\Tests\DuCollection;

use Ehimen\DuCollection\DuCollection;
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
    
    public function testAddThrowsIfNotOfClass()
    {
        $collection = $this->getTestDuCollection();
        $this->setExpectedException(\InvalidArgumentException::class);
        $collection->add(new Blog());
    }
    
    /**
     * @dataProvider providePrimitives
     */
    public function testAddThrowsIfNotObject($primitive)
    {
        $collection = $this->getTestDuCollection();
        $this->setExpectedException(\InvalidArgumentException::class);
        $collection->add($primitive);
    }
    
    public function providePrimitives() : array
    {
        return [
            [null],
            [false],
            [0],
            [''],
            [[]],
        ];
    }
    
    
    private function getTestDuCollection($className = User::class)
    {
        return new DuCollection($className);
    }
    
}