<?php

namespace Ehimen\Tests\DuCollection;

use Ehimen\DuCollection\DuCollection;
use Ehimen\Tests\DuCollection\Fixtures\Blog;
use Ehimen\Tests\DuCollection\Fixtures\Incrementer;
use Ehimen\Tests\DuCollection\Fixtures\MutableCounter;
use Ehimen\Tests\DuCollection\Fixtures\User;
use phpDocumentor\Reflection\DocBlock\Type\Collection;


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
    
    public function testInitialisableFromInstance()
    {
        $this->getTestDuCollection(new User());
        $this->assertInstanceOf(DuCollection::class, $this->getTestDuCollection(new User()));
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
    
    public function testCallsMethodOnContainedObjects()
    {
        $collection = $this->getTestDuCollection(User::class);
        
        $collection->add($this->getMockUserExpectingGetUsernameCall());
        $collection->add($this->getMockUserExpectingGetUsernameCall());
        $collection->add($this->getMockUserExpectingGetUsernameCall());
        
        $collection->getUsername();
    }
    
    public function testCallsMethodOnContainedObjectsWithArgument()
    {
        $collection = $this->getTestDuCollection(User::class);
        
        $collection->add($this->getMockUserExpectingUsernameSetTo('foo'));
        $collection->add($this->getMockUserExpectingUsernameSetTo('foo'));
        $collection->add($this->getMockUserExpectingUsernameSetTo('foo'));
        
        $collection->setUsername('foo');
    }
    
    public function testCallsMethodOnContainedObjectsWithMultipleArguments()
    {
        $collection = $this->getTestDuCollection(User::class);
        
        $date   = new \DateTimeImmutable();
        $action = 'login';
        
        $collection->add($this->getMockUserExpectingActionPerformed($date, $action));
        $collection->add($this->getMockUserExpectingActionPerformed($date, $action));
        $collection->add($this->getMockUserExpectingActionPerformed($date, $action));
        
        $collection->performAction($date, $action);
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
    
    
    private function getTestDuCollection($class = User::class)
    {
        return new DuCollection($class);
    }
    
    private function getMockUserExpectingGetUsernameCall() : User
    {
        $user = $this->getMockBuilder(User::class)
            ->getMock();
        
        $user->expects($this->once())
            ->method('getUsername');
        
        return $user;
    }
    
    private function getMockUserExpectingUsernameSetTo($username) : User
    {
        $user = $this->getMockBuilder(User::class)
            ->getMock();
        
        $user->expects($this->once())
            ->method('setUsername')
            ->with($username);
        
        return $user;
    }
    
    private function getMockUserExpectingActionPerformed(\DateTimeImmutable $date, string $action) : User
    {
        $user = $this->getMockBuilder(User::class)
            ->getMock();
        
        $user->expects($this->once())
            ->method('performAction')
            ->with($date, $action);
        
        return $user;
    }
    
}