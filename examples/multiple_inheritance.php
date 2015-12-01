<?php

require __DIR__ . '/../vendor/autoload.php';

use Ehimen\Duc\DuClass\DuClass;

$parentOne = new DuClass(new class() {
    
    protected $parentOneProperty;
    
    public function parentOneMethod()
    {
        return $this->parentOneProperty;
    }
    
});

$parentTwo = new DuClass(new class() {
    
    protected $parentTwoProperty = 'foo';
    
});

$child = new DuClass(new class(null) {
    
    public function __construct($property)
    {
        $this->parentOneProperty = $property;
    }
    
    public function childMethod()
    {
        return $this->parentTwoProperty;
    }
    
});

// Inherit from both classes.
$child->mixin($parentOne);
$child->mixin($parentTwo);

// new() arguments passed to constructor.
$instance = $child->new('bar');

echo $instance->parentOneMethod() . "\n";   // Method defined in parent one, value set in child constructor.
echo $instance->childMethod() . "\n";       // Value initialised in parent two, method defined in child.
