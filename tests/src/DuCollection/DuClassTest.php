<?php

namespace Ehimen\DucTests\DuCollection;

use Ehimen\Duc\DuClass\DuClass;
use Ehimen\DucTests\TestCase;

class DuClassTest extends TestCase
{
    
    public function testInitialisable()
    {
        $this->assertInstanceOf(DuClass::class, $this->getTestDuClass());
    }
    
    
    /**
     * @dataProvider providePrimitives
     */
    public function testInitialisationThrowsWhenNotObjectProvided($primitive)
    {
        $this->setExpectedException(\InvalidArgumentException::class);
        new DuClass($primitive);
    }
    
    public function testCreatesNewInstance()
    {
        $class = $this->getTestDuClass(new class() {
            public function getValue() : string
            {
                return 'test';
            }
        });
        
        $this->assertSame('test', $class->new()->getValue());
    }
    
    
    public function testCallsMethodsWithArguments()
    {
        $class = $this->getTestDuClass(new class() {
            public function addValue(int $value) : int
            {
                return 1 + $value;
            }
        });
        
        $this->assertSame(4, $class->new()->addValue(3));
    }
    
    
    public function testCallsMethodsWithMultipleArguments()
    {
        $class = $this->getTestDuClass(new class() {
            public function addValue(int $v1, int $v2) : int
            {
                return 1 + $v1 + $v2;
            }
        });
        
        $this->assertSame(8, $class->new()->addValue(3, 4));
    }
    
    public function testInstancesHaveState()
    {
        $class = $this->getTestDuClass(new class() {
            
            public function setValue(int $value)
            {
                $this->value = $value;
            }
    
            public function getValue() : int
            {
                return $this->value;
            }
            
        });
        
        $instance = $class->new();
        
        $instance->setValue(3);
        
        $this->assertSame(3, $instance->getValue());
    }
    
    
    public function testClassCallsMixinMethods()
    {
        $mixin = $this->getTestDuClass(new class() {
            public function getValue()
            {
                return 'foobar';
            }
        });
        
        $class = $this->getTestDuClass(new class() {});
        $class->mixin($mixin);
        
        $this->assertSame('foobar', $class->new()->getValue());
    }
    
    
    public function testClassSharesStateWithMixins()
    {
        $mixin = $this->getTestDuClass(new class() {
            
            protected $value;
            
            public function setValue(int $value)
            {
                $this->value = $value;
            }
        });
        
        $class = $this->getTestDuClass(new class() {
    
            public function getValue() : int
            {
                return $this->value;
            }
            
        });
        
        $class->mixin($mixin);
    
        $instance = $class->new();
        $instance->setValue(12);
        
        $this->assertSame(12, $instance->getValue());
    }
    
    public function testClassUsesMixinPropertyInitialValues()
    {
        $mixin = $this->getTestDuClass(new class() {
            protected $value = 5;
        });
    
        $class = $this->getTestDuClass(new class() {
        
            public function getValue() : int
            {
                return $this->value;
            }
        
        });
    
        $class->mixin($mixin);
    
        $instance = $class->new();
    
        $this->assertSame(5, $instance->getValue());
    }
    
    public function testClassCannotSeeInheritedPrivateProperties()
    {
        $mixin = $this->getTestDuClass(new class() {
            private $value = 5;
        });
    
        $class = $this->getTestDuClass(new class() {
        
            public function getValue()
            {
                return $this->value;
            }
        
        });
    
        $class->mixin($mixin);
    
        $instance = $class->new();
    
        $this->assertSame(null, $instance->getValue());
    }
    
    public function testNonExistentPropertiesReturnNull()
    {
        $class = $this->getTestDuClass(new class() {
            public function getValue()
            {
                return $this->foo;
            }
        });
        
        $this->assertNull($class->new()->foo);
        $this->assertNull($class->new()->getValue());
    }
    
    
    public function testConstructorCalled()
    {
        $class = $this->getTestDuClass(new class() {
            public function __construct()
            {
                $this->foo = 'bar';
            }
        });
        
        $this->assertSame('bar', $class->new()->foo);
    }
    
    
    public function testConstructorCalledWithArguments()
    {
        $class = $this->getTestDuClass(new class(null, null, null) {
            public function __construct($foo, $bar, $baz)
            {
                $this->foo = $foo;
                $this->bar = $bar;
                $this->baz = $baz;
            }
        });
    
        $instance = $class->new(1, 2, 3);
        
        $this->assertSame(1, $instance->foo);
        $this->assertSame(2, $instance->bar);
        $this->assertSame(3, $instance->baz);
    }
    
    
    private function getTestDuClass($class = null) : DuClass
    {
        $class = $class ?: new class() {};
        return new DuClass($class);
    }
    
}