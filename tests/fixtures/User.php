<?php

namespace Ehimen\Tests\DuCollection\Fixtures;

class User
{
    
    public function setUsername(string $username)
    {
        
    }
    
    public function getUsername() : string
    {
        return 'user';
    }
    
    public function performAction(\DateTimeImmutable $date, string $action) : string
    {
        return sprintf('Performed action %s on %s', $action, $date->format('c'));
    }
    
    protected function protectedMethod()
    {
        
    }
    
    private function privateMethod()
    {
        
    }
    
}