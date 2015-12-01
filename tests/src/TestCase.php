<?php

namespace Ehimen\DucTests;

abstract class TestCase extends \PHPUnit_Framework_TestCase
{
    
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
    
}