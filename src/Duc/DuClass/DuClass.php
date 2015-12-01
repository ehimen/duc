<?php

namespace Ehimen\Duc\DuClass;

class DuClass
{
    
    /**
     * @var \ReflectionClass
     */
    private $reflection;
    
    /**
     * @var static[]
     */
    private $mixins = [];
    
    public function __construct($class)
    {
        try {
            $this->reflection = new \ReflectionClass($class);
        } catch (\ReflectionException $e) {
            throw new \InvalidArgumentException(
                sprintf('%s requires a class name or object', static::class),
                0,
                $e
            );
        }
    }
    
    public function new(...$arguments)
    {
        return new class($this->reflection, $arguments, ...$this->mixins) {
    
            /**
             * @var \ReflectionClass
             */
            private $reflection;
    
            /**
             * @var array
             */
            private $state = [];
    
            /**
             * @var \Closure[]
             */
            private $methods = [];
    
            /**
             * @var \ReflectionClass[]
             */
            private $mixins;
    
            public function __construct(\ReflectionClass $class, array $arguments, \ReflectionClass ...$mixins)
            {
                $this->reflection = $class;
                $this->mixins     = $mixins;
                
                if ($class->hasMethod('__construct')) {
                    $this->__call('__construct', $arguments);
                }
            }
    
            public function __call($name, $arguments)
            {
                $closure = $this->getClosure($name)->bindTo($this);
                
                return $closure(...$arguments);
            }
    
            public function __get($name)
            {
                if (array_key_exists($name, $this->state)) {
                    return $this->state[$name];
                }
                
                foreach ($this->mixins as $mixin) {
                    if ($mixin->hasProperty($name)) {
                        
                        $property = $mixin->getProperty($name);
                        
                        if ($property->isPrivate()) {
                            continue;
                        }
                        
                        $property->setAccessible(true);
                        return $property->getValue($mixin->newInstanceWithoutConstructor()); 
                        
                    }
                }
            }
    
            public function __set(string $name, $value)
            {
                $this->state[$name] = $value;
            }
    
            private function getClosure($name) : \Closure
            {
                if (isset($this->methods[$name])) {
                    return $this->methods[$name];
                }
    
                foreach (array_merge([$this->reflection], $this->mixins) as $reflection) {
                    /** @var \ReflectionClass $reflection */
                    if ($reflection->hasMethod($name)) {
                        
                        $instance = $reflection->newInstanceWithoutConstructor();
                        $method   = $reflection->getMethod($name);
                        
                        return $this->methods[$name] = $method->getClosure($instance);
                        
                    }
                }
                
                // TODO: throw
                
            }
            
        };
    }
    
    public function mixin(DuClass $mixin)
    {
        $this->mixins[] = $mixin->reflection;
    }
    
}