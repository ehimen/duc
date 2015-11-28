<?php

namespace Ehimen\Tests\DuCollection;

use Ehimen\DuCollection\DuCollection;
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
        $collection->add(new class() {});
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
    
    
    public function testCallsMethodOnContainedObjectsWithMultipleArgumentsReturnsArray()
    {
        $collection = $this->getTestDuCollection(User::class);
        
        $date   = new \DateTimeImmutable();
        $action = 'login';
        
        $expectedString = sprintf('Performed action %s on %s', $action, $date->format('c'));
        
        $collection->add(new User());
        $collection->add(new User());
        $collection->add(new User());
        
        $values = $collection->performAction($date, $action);
        
        if (!is_array($values) || count($values) !== 3) {
            $this->fail(sprintf(
                '%s expected returned values to be array of size 3, but got %s',
                __METHOD__,
                gettype($values)
            ));
        }
        
        foreach ($values as $value) {
            $this->assertSame($expectedString, $value);
        }
    }
    
    
    public function testThrowsWhenCallingNonExistentMethodOnContainedClass()
    {
        $collection = $this->getTestDuCollection(User::class);
        $this->setExpectedException(\BadMethodCallException::class);
        $collection->notExists();
    }
    
    
    public function testCallsNonReturningMethodOnContainedObjectsReturnNull()
    {
        $collection = $this->getTestDuCollection(User::class);
        $this->setExpectedException(\BadMethodCallException::class);
        $collection->protectedMethod();
    }
    
    
    public function testThrowsWhenCallingProtectedMethod()
    {
        $collection = $this->getTestDuCollection(User::class);
        $this->setExpectedException(\BadMethodCallException::class);
        $collection->privateMethod();
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