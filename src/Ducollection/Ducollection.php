<?php

namespace Ehimen\Ducollection;

class Ducollection implements \Countable
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
    
    public function __construct(string $class)
    {
        if (!class_exists($class)) {
            throw new \InvalidArgumentException(sprintf(
                '%s must be constructed with the name of an existing class. Got: %s',
                get_class($this),
                $class
            ));
        }
        
        $this->class = $class;
        $this->items = new \SplObjectStorage();
    }
    
    public function add($object)
    {
        if (!is_a($object, $this->class)) {
            throw new \InvalidArgumentException(sprintf(
                '%s requires instance of %s. Got: %s',
                get_class($this),
                $this->class,
                get_class($object)
            ));
        }
        
        $this->items->attach($object);
    }
    
    public function count()
    {
        return $this->items->count();
    }
    
}