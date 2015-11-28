<?php

namespace Ehimen\DuCollection;

/**
 * Duck-typed collection.
 *
 * All contained items are instances of a single class.
 * Calls on the collection will be proxied to the contained items.
 */
final class DuCollection implements \Countable
{
    
    /**
     * @var string
     *
     * The name of the class the collection holds.
     */
    private $class;
    
    /**
     * @var \SplObjectStorage
     *
     * Internal collection of objects.
     */
    private $items;
    
    
    /**
     * DuCollection constructor.
     *
     * @param object|string $class An instance of, or the name of the class, that this collection will contain.
     */
    public function __construct($class)
    {
        if (is_object($class)) {
            $class = get_class($class);
        }
        
        if (!class_exists($class)) {
            throw new \InvalidArgumentException(sprintf(
                '%s must be constructed with the name of an existing class. Got: %s',
                static::class,
                $class
            ));
        }
        
        $this->class = $class;
        $this->items = new \SplObjectStorage();
    }
    
    
    /**
     * Adds an object to the collection.
     *
     * $object must be an instance of the class of this collection.
     *
     * @param object $object The object to add.
     */
    public function add($object)
    {
        if (!is_object($object)) {
            throw new \InvalidArgumentException(sprintf(
                '%s requires an object. Got: %s',
                __METHOD__,
                gettype($object)
            ));
        }
        
        if (!is_a($object, $this->class)) {
            throw new \InvalidArgumentException(sprintf(
                '%s requires instance of %s. Got: %s',
                __METHOD__,
                $this->class,
                get_class($object)
            ));
        }
        
        $this->items->attach($object);
    }
    
    
    /**
     * Magic method to proxy calls to each of the contained elements.
     *
     * @param string $name The method name.
     * @param array  $arguments
     *
     * @throws \BadMethodCallException  When the method cannot be found for this collection's class, or is non-public.
     *
     * @return array|null
     */
    public function __call(string $name, array $arguments)
    {
        $method = $this->getReflectionMethod($name);
        
        if (!$method->isPublic()) {
            throw new \BadMethodCallException(sprintf(
                '%s cannot invoke non-public method %s',
                $this->getDescription(),
                $name
            ));
        }
        
        $values = [];
        
        foreach ($this->items as $item) {
            $values[] = $item->$name(...$arguments);
        }
        
        if (!$method->getReturnType()) {
            return null;
        }
        
        return $values;
    }
    
    
    /**
     * @inheritdoc
     */
    public function count()
    {
        return $this->items->count();
    }
    
    
    /**
     * Gets a reflection method by $name for the class that this collection contains.
     *
     * @param string $name
     *
     * @return \ReflectionMethod
     */
    private function getReflectionMethod(string $name) : \ReflectionMethod
    {
        if (!method_exists($this->class, $name)) {
            throw new \BadMethodCallException(sprintf(
                '%s cannot invoke unknown method %s',
                $this->getDescription(),
                $name
            ));
        }
        
        return new \ReflectionMethod($this->class, $name);
    }
    
    
    /**
     * Gets a description of this class.
     *
     * Useful for exception messages.
     *
     * @return string
     */
    private function getDescription()
    {
        return sprintf('%s (class: %s)', static::class, $this->class);
    }
    
}